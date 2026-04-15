<?php

namespace App\Services;

use App\Enums\CandidateStatus;
use App\Models\Candidate;
use App\Models\User;
use App\Models\OnboardingTemplate;
use App\Models\ActivityLog;
use App\Jobs\SendCandidateEmail;
use App\Jobs\ProcessNoResponseFollowup;
use App\Notifications\AdminActivityNotification;
use Illuminate\Http\UploadedFile;

class CandidateService
{
    /**
     * Create a new candidate from manual entry or resume upload.
     */
    public function create(array $data, ?UploadedFile $resumeFile = null): Candidate
    {
        // Round-robin assignment
        $assignee = User::nextAssignee();

        $candidate = Candidate::create([
            'first_name'      => $data['first_name'],
            'last_name'       => $data['last_name'],
            'email'           => $data['email'] ?? null,
            'phone'           => $data['phone'] ?? null,
            'job_category_id' => $data['job_category_id'] ?? null,
            'source'          => $data['source'] ?? 'Website',
            'status'          => CandidateStatus::NEEDS_REVIEW,
            'assigned_to'     => $assignee?->id,
            'notes'           => $data['notes'] ?? null,
            'resume_text'     => $data['resume_text'] ?? null,
        ]);

        // Handle resume file upload
        if ($resumeFile) {
            $relative = "resumes/{$candidate->id}";
            $filename  = time().'_'.$resumeFile->getClientOriginalName();
            $resumeFile->move(public_path($relative), $filename);
            $path = "{$relative}/{$filename}";

            $candidate->update(['resume_file' => $path]);

            $candidate->documents()->create([
                'name'      => $resumeFile->getClientOriginalName(),
                'type'      => 'resume',
                'file_path' => $path,
                'mime_type' => $resumeFile->getClientMimeType(),
                'file_size' => filesize(public_path($path)),
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

        // Trigger side-effect actions based on new status
        match ($newStatus) {
            CandidateStatus::INVITE_SENT => $this->onInviteSent($candidate),
            CandidateStatus::INTERVIEW_SCHEDULED => $this->onInterviewScheduled($candidate),
            CandidateStatus::PRE_SCREENING_PASSED => $this->onPreScreeningPassed($candidate),
            CandidateStatus::OFFER_SENT => $this->onOfferSent($candidate),
            CandidateStatus::OFFER_ACCEPTED => $this->onOfferAccepted($candidate),
            CandidateStatus::REJECTED => $this->onRejected($candidate),
            CandidateStatus::APPLICANT_DECLINED => $this->onDeclined($candidate),
            CandidateStatus::ONBOARDING => $this->onOnboarding($candidate),
            default => null,
        };

        return $candidate;
    }

    protected function onInviteSent(Candidate $candidate): void
    {
        $candidate->update(['invite_sent_at' => now()]);
        SendCandidateEmail::dispatch($candidate, 'invite');

        // Schedule no-response follow-up check
        $followupDays = (int) \App\Models\Setting::get('followup_days', 5);
        ProcessNoResponseFollowup::dispatch($candidate)
            ->delay(now()->addDays($followupDays));
    }

    protected function onInterviewScheduled(Candidate $candidate): void
    {
        SendCandidateEmail::dispatch($candidate, 'interview_confirmation');
    }

    protected function onPreScreeningPassed(Candidate $candidate): void
    {
        // Send application, BG consent, reference forms after 2-day delay
        SendCandidateEmail::dispatch($candidate, 'prescreening')
            ->delay(now()->addDays(2));

        // Create background check records
        foreach (['mdhhs', 'sam_oig', 'npdb'] as $type) {
            $candidate->backgroundChecks()->firstOrCreate(
                ['check_type' => $type],
                ['status' => 'pending']
            );
        }
    }

    protected function onOfferSent(Candidate $candidate): void
    {
        SendCandidateEmail::dispatch($candidate, 'offer');
    }

    protected function onOfferAccepted(Candidate $candidate): void
    {
        SendCandidateEmail::dispatch($candidate, 'onboarding');
        $this->createOnboardingTasks($candidate);
    }

    protected function onRejected(Candidate $candidate): void
    {
        SendCandidateEmail::dispatch($candidate, 'reject');
    }

    protected function onDeclined(Candidate $candidate): void
    {
        SendCandidateEmail::dispatch($candidate, 'declined_followup');
    }

    protected function onOnboarding(Candidate $candidate): void
    {
        if ($candidate->onboardingTasks()->count() === 0) {
            $this->createOnboardingTasks($candidate);
        }
    }

    /**
     * Create onboarding tasks from templates.
     */
    protected function createOnboardingTasks(Candidate $candidate): void
    {
        $templates = OnboardingTemplate::where('is_active', true)
            ->orderBy('sort_order')->get();

        if ($templates->isEmpty()) {
            // Use defaults if no templates configured
            $defaults = [
                'Upload signed offer letter',
                'Complete background check consent',
                'Submit references',
                'Upload credentials & licenses',
                'Complete I-9 verification',
                'Upload driver\'s license',
                'Select orientation date',
                'Setup email account',
                'Building access & WiFi',
                'Review employee handbook',
                'Complete initial training modules',
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
