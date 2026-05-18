<?php

namespace App\Services;

use App\Enums\CandidateStatus;
use App\Models\Candidate;
use App\Models\InterviewAvailabilitySlot;
use App\Models\Setting;
use App\Models\User;
use App\Models\OnboardingTemplate;
use App\Models\ActivityLog;
use App\Jobs\SendCandidateEmail;
use App\Notifications\AdminActivityNotification;
use App\Models\EmailTemplate;
use App\Models\Offer;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class CandidateService
{
    /**
     * Create a new candidate from manual entry or resume upload.
     */
    public function create(array $data, ?UploadedFile $resumeFile = null, ?UploadedFile $authBgFile = null): Candidate
    {
        // Round-robin assignment
        $assignee = User::nextAssignee();

        $candidate = Candidate::create([
            'first_name'      => $data['first_name'],
            'last_name'       => $data['last_name'],
            'email'           => $data['email'] ?? null,
            'phone'           => $data['phone'] ?? null,
            'street_address'  => $data['street_address'] ?? null,
            'city'            => $data['city'] ?? null,
            'state'           => $data['state'] ?? null,
            'postal_code'     => $data['postal_code'] ?? null,
            'job_category_id' => $data['job_category_id'] ?? null,
            'source'          => $data['source'] ?? 'Website',
            'status'          => CandidateStatus::HIRING,
            'assigned_to'     => $assignee?->id,
            'notes'           => $data['notes'] ?? null,
            'resume_text'     => $data['resume_text'] ?? null,
            'linkedin_url'    => $data['linkedin_url'] ?? null,
            'education_level' => $data['education_level'] ?? null,
            'years_experience' => $data['years_experience'] ?? null,
            'is_authorized_to_work' => $data['is_authorized_to_work'] ?? null,
            'desired_pay'     => $data['desired_pay'] ?? null,
            'earliest_start_date' => $data['earliest_start_date'] ?? null,
            'availability'    => $data['availability'] ?? null,
            'clinical_license_expires_at' => $data['clinical_license_expires_at'] ?? null,
        ]);

        // Optional authorization background-check document (Staff Portal create form)
        if ($authBgFile) {
            $dir = public_path("background_authorizations/{$candidate->id}");
            if (! File::exists($dir)) {
                File::makeDirectory($dir, 0755, true, true);
            }
            $filename = time() . '_' . preg_replace('/[^A-Za-z0-9_.-]/', '_', $authBgFile->getClientOriginalName());
            $authBgFile->move($dir, $filename);
            $candidate->update([
                'authorization_background_check_path' => "background_authorizations/{$candidate->id}/{$filename}",
                'authorization_background_check_name' => $authBgFile->getClientOriginalName(),
            ]);
        }

        // Handle resume file upload
        if ($resumeFile) {
            $relative = "resumes/{$candidate->id}";
            $dir = public_path($relative);

            // Ensure the public directory exists so file is web-accessible
            if (! File::exists($dir)) {
                File::makeDirectory($dir, 0755, true, true);
            }

            $filename = time() . '_' . preg_replace('/[^A-Za-z0-9_.-]/', '_', $resumeFile->getClientOriginalName());
            $resumeFile->move($dir, $filename);

            $path = "{$relative}/{$filename}";
            $fullPath = public_path($path);

            $candidate->update(['resume_file' => $path]);

            $candidate->documents()->create([
                'name'      => $resumeFile->getClientOriginalName(),
                'type'      => 'resume',
                'file_path' => $path,
                'mime_type' => $resumeFile->getClientMimeType(),
                'file_size' => is_file($fullPath) ? filesize($fullPath) : null,
            ]);
        }

        // Rotate round-robin
        User::rotateRoundRobin();

        // Log activity
        $this->log($candidate, 'created', null, $candidate->status->value,
            "Candidate added via {$candidate->source}. Assigned to {$assignee?->full_name}.");

        // Notify assigned HR staff
        if ($assignee) {
            $assignee->notify(new \App\Notifications\NewCandidateAssigned($candidate));
        }

        // Notify all admins
        $actor        = auth()->check() ? auth()->user()->full_name : 'System';
        $assigneeName = $assignee?->full_name ?? 'nobody';
        $this->notifyAdmins(
            '📋 New Application Received',
            "{$candidate->full_name} applied via {$candidate->source}. Assigned to {$assigneeName}.",
            'new_candidate',
            $candidate,
            $actor
        );

        return $candidate;
    }

    /**
     * Change candidate status with all side effects.
     */
    public function changeStatus(Candidate $candidate, CandidateStatus $newStatus, ?int $userId = null): Candidate
    {
        $oldStatus = $candidate->status;
        if ($oldStatus === $newStatus) return $candidate;

        $candidate->update(['status' => $newStatus]);

        $this->log($candidate, 'status_changed', $oldStatus->value, $newStatus->value,
            "Status changed from {$oldStatus->label()} to {$newStatus->label()}");

        // Notify admins about status change
        $actor = auth()->check() ? auth()->user()->full_name : 'System';
        $this->notifyAdmins(
            '🔄 Candidate Status Updated',
            "{$candidate->full_name}: {$oldStatus->label()} → {$newStatus->label()}. By: {$actor}.",
            'status_changed',
            $candidate,
            $actor
        );

        // Trigger side-effect actions based on new status.
        // Both portal stages and workflow statuses map to the same handlers so HR
        // can use either vocabulary and get the same automation behaviour.
        match ($newStatus) {
            // Portal stages
            CandidateStatus::PRE_SCREENING,
            CandidateStatus::INVITE_SENT               => $this->onPreScreening($candidate),

            CandidateStatus::PRE_INTERVIEW_QUESTIONS,
            CandidateStatus::PRE_SCREENING_PASSED      => $this->onPreInterviewQuestions($candidate),

            CandidateStatus::VERIFICATION_AND_REVIEW,
            CandidateStatus::AWAITING_BACKGROUND_CHECK => $this->onVerificationAndReview($candidate),

            CandidateStatus::OFFER_LETTER,
            CandidateStatus::OFFER_SENT                => $this->onOfferLetter($candidate),

            CandidateStatus::PRE_ONBOARD_DOCUMENTS,
            CandidateStatus::OFFER_ACCEPTED,
            CandidateStatus::PRE_ONBOARD               => $this->onPreOnboardDocuments($candidate),

            CandidateStatus::REJECTED,
            CandidateStatus::NOT_SELECTED              => $this->onRejected($candidate),

            CandidateStatus::APPLICANT_DECLINED,
            CandidateStatus::OFFER_DECLINED            => $this->onDeclined($candidate),

            CandidateStatus::HIRED,
            CandidateStatus::ACTIVE_STAFF              => $this->onHired($candidate),

            default                                    => null,
        };

        return $candidate;
    }

    protected function onPreScreening(Candidate $candidate): void
    {
        // Generate a unique token for the candidate's scheduling link
        if (! $candidate->schedule_token) {
            $candidate->update(['schedule_token' => \Illuminate\Support\Str::uuid()->toString()]);
        }

        $candidate->update(['invite_sent_at' => now()]);
        $this->generateAvailabilitySlots($candidate);
        SendCandidateEmail::dispatchSync($candidate, 'invite');
        // No-response follow-up is handled by the daily scheduler (app:process-automations).
    }

    /**
     * Expand the weekly availability template into concrete InterviewAvailabilitySlot
     * records for the next 30 days. Existing unbooked future slots are cleared first
     * so re-sending an invite regenerates a fresh set.
     */
    protected function generateAvailabilitySlots(Candidate $candidate): void
    {
        $weekly   = json_decode(Setting::get('weekly_availability', '{}'), true);
        $duration = (int) Setting::get('interview_duration', 45);

        if (empty($weekly) || $duration < 5) {
            return;
        }

        $timezone = Setting::get('timezone', 'America/New_York');
        $now      = now($timezone);

        // Clear any existing unbooked future slots to avoid duplicates on resend
        InterviewAvailabilitySlot::where('candidate_id', $candidate->id)
            ->whereNull('booked_interview_id')
            ->where('starts_at', '>', $now)
            ->delete();

        $lookahead = $now->copy()->addDays(30)->endOfDay();
        $current   = $now->copy()->startOfDay();
        $slots     = [];

        while ($current->lessThanOrEqualTo($lookahead)) {
            $dayName = strtolower($current->format('l')); // 'monday', 'tuesday', …
            $config  = $weekly[$dayName] ?? null;

            if ($config && ! empty($config['enabled']) && ! empty($config['start']) && ! empty($config['end'])) {
                [$startH, $startM] = explode(':', $config['start']);
                [$endH,   $endM]   = explode(':', $config['end']);

                $slotStart = $current->copy()->setHour((int) $startH)->setMinute((int) $startM)->setSecond(0);
                $dayEnd    = $current->copy()->setHour((int) $endH)->setMinute((int) $endM)->setSecond(0);

                while ($slotStart->copy()->addMinutes($duration)->lessThanOrEqualTo($dayEnd)) {
                    $slotEnd = $slotStart->copy()->addMinutes($duration);

                    if ($slotEnd->isAfter($now)) {
                        $slots[] = [
                            'candidate_id' => $candidate->id,
                            'starts_at'    => $slotStart->copy()->utc(),
                            'ends_at'      => $slotEnd->copy()->utc(),
                            'created_by'   => auth()->id(),
                            'created_at'   => now(),
                            'updated_at'   => now(),
                        ];
                    }

                    $slotStart->addMinutes($duration);
                }
            }

            $current->addDay();
        }

        if (! empty($slots)) {
            InterviewAvailabilitySlot::insert($slots);
        }
    }

    protected function onPreInterviewQuestions(Candidate $candidate): void
    {
        if (! $candidate->prescreen_token) {
            $candidate->update(['prescreen_token' => Str::uuid()->toString()]);
        }

        SendCandidateEmail::dispatchSync($candidate, 'prescreening');
    }

    protected function onVerificationAndReview(Candidate $candidate): void
    {
        // Create background check records
        foreach (['mdhhs', 'sam_oig', 'npdb'] as $type) {
            $candidate->backgroundChecks()->firstOrCreate(
                ['check_type' => $type],
                ['status' => 'pending']
            );
        }
    }

    protected function onOfferLetter(Candidate $candidate): void
    {
        $offer = $candidate->latestOffer;

        // Auto-create a placeholder offer so it appears in the Offers area and is editable
        if (! $offer) {
            $offer = Offer::create([
                'candidate_id'    => $candidate->id,
                'pay_rate'        => 0.00,
                'pay_type'        => 'hourly',
                'employment_type' => 'Full-Time',
                'status'          => 'sent',
                'sent_at'         => now(),
                'created_by'      => auth()->id(),
                'token'           => Str::uuid()->toString(),
                'deadline_days'   => (int) \App\Models\Setting::get('offer_deadline', 20),
            ]);
        }

        $extraVars = [];
        if ($offer->token) {
            $baseUrl = rtrim(\App\Models\Setting::get('app_url', config('app.url')), '/');
            $extraVars['offer_link'] = $baseUrl . '/offer/' . $offer->token;
        }

        SendCandidateEmail::dispatchSync($candidate, 'offer', $extraVars);
    }

    protected function onPreOnboardDocuments(Candidate $candidate): void
    {
        SendCandidateEmail::dispatchSync($candidate, 'onboarding');

        if ($candidate->onboardingTasks()->count() === 0) {
            $this->createOnboardingTasks($candidate);
        }

        // Notify HR staff assigned to this candidate
        $actor = $candidate->full_name;
        $this->notifyAdmins(
            '✅ Offer Accepted',
            "{$candidate->full_name} has accepted the offer. Start onboarding.",
            'offer_accepted',
            $candidate,
            $actor
        );
    }

    protected function onRejected(Candidate $candidate): void
    {
        SendCandidateEmail::dispatchSync($candidate, 'reject');
    }

    protected function onDeclined(Candidate $candidate): void
    {
        SendCandidateEmail::dispatchSync($candidate, 'declined_followup');

        $this->notifyAdmins(
            '❌ Offer Declined',
            "{$candidate->full_name} has declined the offer.",
            'offer_declined',
            $candidate,
            $candidate->full_name
        );
    }

    protected function onHired(Candidate $candidate): void
    {
        // Guard: if convertToEmployee was already called (employee record exists), skip
        if (\App\Models\Employee::where('candidate_id', $candidate->id)->exists()) {
            return;
        }

        if (! $candidate->email) return;

        $tempPassword = Str::random(10);
        $offer        = $candidate->latestOffer;

        $user = User::updateOrCreate(
            ['email' => $candidate->email],
            [
                'first_name' => $candidate->first_name,
                'last_name'  => $candidate->last_name,
                'password'   => Hash::make($tempPassword),
                'role'       => 'employee',
                'is_active'  => true,
            ]
        );

        \App\Models\Employee::create([
            'candidate_id'    => $candidate->id,
            'user_id'         => $user->id,
            'first_name'      => $candidate->first_name,
            'last_name'       => $candidate->last_name,
            'email'           => $candidate->email,
            'phone'           => $candidate->phone,
            'role'            => $candidate->category?->name ?? 'Staff',
            'employment_type' => $offer?->employment_type ?? 'Full-Time',
            'start_date'      => $offer?->start_date ?? now()->toDateString(),
            'pay_rate'        => $offer?->pay_rate,
            'pay_type'        => $offer?->pay_type ?? 'hourly',
            'location'        => $offer?->location,
            'is_active'       => true,
        ]);

        SendCandidateEmail::dispatchSync($candidate, 'portal_credentials', [
            'login_url'     => config('app.url') . '/login',
            'login_email'   => $candidate->email,
            'temp_password' => $tempPassword,
            'door_code'     => \App\Models\Setting::get('door_code', 'See HR for details'),
            'wifi_password' => \App\Models\Setting::get('wifi_password', 'See HR for details'),
        ]);
    }

    /**
     * Create onboarding tasks from templates.
     */
    public function createOnboardingTasks(Candidate $candidate): void
    {
        $templates = OnboardingTemplate::where('is_active', true)
            ->orderBy('sort_order')->get();

        if ($templates->isEmpty()) {
            // Use defaults if no templates configured
            $defaults = [
                'Upload Signed Offer Letter',
                'Complete Background Check Consent',
                'Submit References',
                'Upload Credentials & Licenses',
                'Complete I-9 Verification',
                'Upload Driver\'s License',
                'Collect Emergency Contact Details',
                'Select Orientation Date',
                'Setup Email Account',
                'Building Access & WiFi',
                'Review Employee Handbook',
                'Complete Initial Training Modules',
            ];
            foreach ($defaults as $i => $name) {
                $candidate->onboardingTasks()->create([
                    'task_name'  => $name,
                    'sort_order' => $i,
                ]);
            }
        } else {
            foreach ($templates as $template) {
                $candidate->onboardingTasks()->create([
                    'template_id' => $template->id,
                    'task_name'   => $template->name,
                    'sort_order'  => $template->sort_order,
                ]);
            }
        }
    }

    /**
     * Convert a hired candidate into an employee.
     */
    public function convertToEmployee(Candidate $candidate, array $extraData = []): \App\Models\Employee
    {
        $offer = $candidate->latestOffer;

        $employee = \App\Models\Employee::create(array_merge([
            'candidate_id'    => $candidate->id,
            'first_name'      => $candidate->first_name,
            'last_name'       => $candidate->last_name,
            'email'           => $candidate->email,
            'phone'           => $candidate->phone,
            'role'            => $candidate->category?->name ?? 'Staff',
            'employment_type' => $offer?->employment_type ?? 'Full-Time',
            'start_date'      => $offer?->start_date ?? now(),
            'pay_rate'        => $offer?->pay_rate,
            'pay_type'        => $offer?->pay_type ?? 'hourly',
            'location'        => $offer?->location,
            'is_active'       => true,
        ], $extraData));

        // Create (or update) a User account so the employee can log in to the portal
        $accessInfo   = $extraData['access_info'] ?? [];
        $tempPassword = $accessInfo['temp_password'] ?? null;

        if ($candidate->email && $tempPassword) {
            $user = User::updateOrCreate(
                ['email' => $candidate->email],
                [
                    'first_name' => $candidate->first_name,
                    'last_name'  => $candidate->last_name,
                    'password'   => Hash::make($tempPassword),
                    'role'       => 'employee',
                    'is_active'  => true,
                ]
            );

            $employee->update(['user_id' => $user->id]);

            // Ensure the portal_credentials template exists (safe on existing databases)
            EmailTemplate::firstOrCreate(
                ['slug' => 'portal_credentials'],
                [
                    'name'    => 'Portal Credentials',
                    'subject' => 'Your {{company_name}} Employee Portal Access',
                    'body'    => "Dear {{candidate_name}},\n\nCongratulations and welcome to {{company_name}}!\n\nYour employee portal access has been created:\n\nLogin URL:          {{login_url}}\nEmail:              {{login_email}}\nTemporary Password: {{temp_password}}\n\nBuilding Access: {{door_code}}\nWiFi Password:   {{wifi_password}}\n\nPlease log in and change your password on first sign-in.\n\nBest regards,\n{{hr_name}}",
                ]
            );

            SendCandidateEmail::dispatchSync($candidate, 'portal_credentials', [
                'login_url'     => config('app.url') . '/login',
                'login_email'   => $candidate->email,
                'temp_password' => $tempPassword,
                'door_code'     => $accessInfo['door_code']     ?? 'See HR for details',
                'wifi_password' => $accessInfo['wifi_password'] ?? 'See HR for details',
            ]);
        }

        $this->changeStatus($candidate, CandidateStatus::HIRED);

        $actor = auth()->check() ? auth()->user()->full_name : 'System';
        $this->notifyAdmins(
            '🎉 Candidate Converted to Employee',
            "{$candidate->full_name} has been converted to an employee by {$actor}.",
            'converted_to_employee',
            $candidate,
            $actor
        );

        return $employee;
    }

    /**
     * Send a notification to all active admin users.
     */
    protected function notifyAdmins(string $title, string $message, string $type, Candidate $candidate, ?string $actor = null): void
    {
        $notification = new AdminActivityNotification($title, $message, $type, $candidate->id, $actor);

        \App\Models\User::where('role', 'admin')
            ->where('is_active', true)
            ->get()
            ->each(fn($admin) => $admin->notify($notification));
    }

    protected function log(Candidate $candidate, string $action, ?string $old, ?string $new, ?string $desc = null): void
    {
        $candidate->activityLogs()->create([
            'user_id'     => auth()->id(),
            'action'      => $action,
            'old_value'   => $old,
            'new_value'   => $new,
            'description' => $desc,
        ]);
    }
}
