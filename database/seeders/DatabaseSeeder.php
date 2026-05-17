<?php

namespace Database\Seeders;

use App\Models\AutomationRule;
use App\Models\Candidate;
use App\Models\EmailTemplate;
use App\Models\Employee;
use App\Models\JobCategory;
use App\Models\OnboardingTemplate;
use App\Models\Setting;
use App\Models\Training;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Settings ──
        $settings = [
            'company_name'       => 'Wellness Behavioral Health',
            'timezone'           => 'America/Detroit',
            'interview_duration' => '20',
            'offer_deadline'     => '20',
            'followup_days'      => '5',
            'queue_days'         => '10',
            'hiring_position_description' => <<<TXT
Supports Coordination: Bachelor's in Human Services, QIDP, and Michigan Supports Coordinator training. Must have 1+ year of experience coordinating care plans, conducting assessments, and managing community resources for individuals with disabilities.

Supports Coordination Assistant: High School Diploma. Must have 1+ year of experience providing administrative support in a social services or healthcare setting, including data entry and client scheduling.

Psychological Evaluations: Must have at least 1 year of hands-on experience administering cognitive and adaptive testing (e.g., WISC, Vineland) specifically for ASD, ADHD, IDD, and Guardianship cases.

Psychotherapy: Active licensure (LMSW, LPC, TLLP, LLP, or LP) with 1+ year of clinical experience providing individual therapy, crisis intervention, and treatment planning.

Group Therapy: Active licensure (LMSW, LPC, TLLP, LLP, or LP) with 1+ year of experience facilitating therapeutic groups, managing group dynamics, and documenting group sessions.

Masters Level Clinician: Active Master's degree and licensure (LMSW, LPC, TLLP, LLP) with 1+ year of post-graduate clinical experience in assessment, diagnosis, and evidence-based treatment.

Office Assistant: High School Diploma. Must have 1+ year of experience in medical or social service office administration, including phone triage, filing, and front-desk operations.

Financial Management – Claims Specialist: Must have 1+ year of experience processing Medicaid/Medicare claims, handling billing denials, and reconciling accounts in a healthcare or human services environment.

Financial Management – CPA: Must hold an active CPA license with 1+ year of experience in financial auditing, budget management, and regulatory compliance for non-profit or healthcare organizations.

Building Maintenance: Must have 1+ year of experience performing general facility repairs, HVAC basics, plumbing, electrical troubleshooting, and preventative maintenance in a commercial or residential setting.

Communication Person: Must have 1+ year of experience in public relations, media outreach, or internal communications, with strong writing skills and experience managing social media or newsletters.

HR / Quality Assurance: Must have 1+ year of experience in human resources (recruiting, onboarding, compliance) and QA (auditing clinical records, ensuring regulatory adherence) within a healthcare or social service agency.
TXT,
        ];
        foreach ($settings as $k => $v) {
            Setting::firstOrCreate(['key' => $k], ['value' => $v]);
        }

        // ── HR Staff ──
        $staff = [
            ['first_name' => 'Admin',   'last_name' => 'User',    'email' => 'admin@hris.com',   'role' => 'admin',    'round_robin_order' => 1],
            ['first_name' => 'Sarah',   'last_name' => 'Johnson',  'email' => 'sjohnson@hris.com', 'role' => 'hr_staff', 'round_robin_order' => 2],
            ['first_name' => 'Marcus',  'last_name' => 'Lee',      'email' => 'mlee@hris.com',     'role' => 'hr_staff', 'round_robin_order' => 3],
            ['first_name' => 'Denise',  'last_name' => 'Harris',   'email' => 'dharris@hris.com',  'role' => 'hr_staff', 'round_robin_order' => 4],
        ];
        $staffIds = [];
        foreach ($staff as $s) {
            $user = User::firstOrCreate(['email' => $s['email']], array_merge($s, ['password' => Hash::make('password'), 'is_active' => true]));
            $staffIds[] = $user->id;
        }

        // ── Job Categories ──
        $cats = ['Licensed Clinician', 'Masters Level', 'Bachelors Level', 'Supports Coordinator', 'Administrative'];
        foreach ($cats as $c) {
            JobCategory::firstOrCreate(['slug' => \Str::slug($c)], ['name' => $c]);
        }

        // ── Onboarding Templates ──
        $onbSteps = [
            'Upload signed offer letter', 'Complete background check consent', 'Submit references',
            'Upload credentials & licenses', 'Complete I-9 verification', "Upload driver's license",
            'Select orientation date', 'Setup email account', 'Building access & WiFi credentials',
            'Review employee handbook', 'Complete initial training modules',
        ];
        foreach ($onbSteps as $i => $name) {
            OnboardingTemplate::firstOrCreate(['name' => $name], ['sort_order' => $i]);
        }

        // ── Email Templates ──
        $templates = [
            ['slug' => 'invite',              'name' => 'Interview Invite',          'subject' => 'Interview Invitation — {{company_name}}',       'body' => "Dear {{candidate_name}},\n\nThank you for your interest in the {{role}} position at {{company_name}}.\n\nPlease select a time: {{scheduling_link}}\n\nThe interview is 15-20 min via Zoom.\n\nBest regards,\n{{hr_name}}"],
            ['slug' => 'followup',            'name' => 'No Response Follow-up',     'subject' => 'Following Up — {{company_name}}',               'body' => "Hi {{candidate_name}},\n\nWe recently reached out about the {{role}} position. Still interested?\n\nSchedule here: {{scheduling_link}}\n\nBest,\n{{hr_name}}"],
            ['slug' => 'reject',              'name' => 'Rejection',                 'subject' => 'Application Update — {{company_name}}',         'body' => "Dear {{candidate_name}},\n\nThank you for your interest. After review, we've moved forward with other candidates.\n\nBest wishes,\n{{hr_name}}"],
            ['slug' => 'prescreening',        'name' => 'Pre-Screening Next Steps',  'subject' => 'Next Steps — {{company_name}}',                 'body' => "Dear {{candidate_name}},\n\nCongratulations on advancing for {{role}}!\n\nPlease complete your post-interview application form here:\n{{prescreening_link}}\n\nOnce submitted, our team will review and contact you with next steps.\n\nBest,\n{{hr_name}}"],
            ['slug' => 'reference',           'name' => 'Reference Request',         'subject' => 'Reference Request for {{candidate_name}}',      'body' => "Dear {{reference_name}},\n\n{{candidate_name}} listed you as a reference at {{company_name}}.\n\n1. How long have you known them?\n2. What capacity?\n3. Strengths?\n4. Recommend?\n\nThank you,\n{{hr_name}}"],
            ['slug' => 'offer',               'name' => 'Offer Letter',              'subject' => 'Offer Letter — {{company_name}}',               'body' => "Dear {{candidate_name}},\n\nWe are pleased to offer you the position of {{role}} at {{company_name}}.\n\nOffer Details:\n\u2022 Pay Rate:         {{offer_pay_rate}}\n\u2022 Employment Type:  {{offer_employment_type}}\n\u2022 Location:         {{location}}\n\u2022 Start Date:       {{offer_start_date}}\n\u2022 Orientation Date: {{offer_orientation_date}}\n\nTo view, ACCEPT, or DECLINE this offer, please visit:\n{{offer_link}}\n\nPlease respond before the deadline. If you have any questions, contact your HR representative.\n\nWarm regards,\n{{hr_name}}\n{{company_name}}"],
            ['slug' => 'onboarding',          'name' => 'Onboarding Welcome',        'subject' => 'Welcome to {{company_name}}!',                  'body' => "Dear {{candidate_name}},\n\nWelcome! Here's what you need:\n- Email login info\n- WiFi credentials\n- Building access: front desk day 1\n\nComplete all onboarding tasks in your portal.\n\nBest,\n{{hr_name}}"],
            ['slug' => 'interview_confirmation', 'name' => 'Interview Confirmation', 'subject' => 'Interview Confirmed — {{company_name}}',       'body' => "Dear {{candidate_name}},\n\nYour interview for {{role}} is confirmed.\n\nDetails will be sent shortly.\n\nBest,\n{{hr_name}}"],
            ['slug' => 'declined_followup',   'name' => 'Declined Follow-up',        'subject' => 'Thank You — {{company_name}}',                  'body' => "Dear {{candidate_name}},\n\nWe understand your decision. We'd love to stay in touch for future opportunities.\n\nBest,\n{{hr_name}}"],
            ['slug' => 'sms_followup',     'name' => 'SMS Follow-up (No Response)',  'subject' => '',                                              'body' => "Hi {{candidate_first_name}}, this is {{hr_name}} from {{company_name}}. Still interested in the {{role}} position? Book a quick interview here: {{scheduling_link}}", 'category' => 'sms'],
            ['slug' => 'portal_credentials',  'name' => 'Portal Credentials',        'subject' => 'Your {{company_name}} Employee Portal Access',   'body' => "Dear {{candidate_name}},\n\nCongratulations and welcome to {{company_name}}!\n\nYour employee portal access has been created:\n\nLogin URL:          {{login_url}}\nEmail:              {{login_email}}\nTemporary Password: {{temp_password}}\n\nBuilding Access: {{door_code}}\nWiFi Password:   {{wifi_password}}\n\nPlease log in and change your password on first sign-in.\n\nBest regards,\n{{hr_name}}"],
        ];
        foreach ($templates as $t) {
            EmailTemplate::firstOrCreate(['slug' => $t['slug']], $t);
        }

        // ── Automation Rules ──
        $rules = [
            ['trigger_event' => 'candidate_created',  'action_type' => 'notify',     'action_config' => ['target' => 'assigned_hr']],
            ['trigger_event' => 'status_changed',     'trigger_value' => 'pre_screening',           'action_type' => 'send_email', 'action_config' => ['template' => 'invite']],
            ['trigger_event' => 'no_response',        'trigger_value' => '5_days',                  'action_type' => 'send_sms',   'action_config' => ['template' => 'sms_followup'], 'delay_hours' => 120],
            ['trigger_event' => 'status_changed',     'trigger_value' => 'pre_interview_questions', 'action_type' => 'send_email', 'action_config' => ['template' => 'prescreening']],
            ['trigger_event' => 'reference_submitted','action_type' => 'send_email', 'action_config' => ['template' => 'reference']],
            ['trigger_event' => 'bg_checks_complete', 'action_type' => 'notify',     'action_config' => ['target' => 'assigned_hr']],
            ['trigger_event' => 'status_changed',     'trigger_value' => 'offer_letter',            'action_type' => 'send_email', 'action_config' => ['template' => 'offer']],
            ['trigger_event' => 'status_changed',     'trigger_value' => 'pre_onboard_documents',   'action_type' => 'send_email', 'action_config' => ['template' => 'onboarding']],
            ['trigger_event' => 'status_changed',     'trigger_value' => 'rejected',                'action_type' => 'send_email', 'action_config' => ['template' => 'reject']],
            ['trigger_event' => 'status_changed',     'trigger_value' => 'applicant_declined',      'action_type' => 'send_email', 'action_config' => ['template' => 'declined_followup']],
            ['trigger_event' => 'training_expiring',  'action_type' => 'notify',     'action_config' => ['days_before' => 30]],
        ];
        foreach ($rules as $r) {
            AutomationRule::firstOrCreate(['trigger_event' => $r['trigger_event'], 'trigger_value' => $r['trigger_value'] ?? null], array_merge(['is_active' => true], $r));
        }

        // ── Sample Candidates ──
        $sampleCandidates = [
            ['first_name'=>'Amara','last_name'=>'Chen','email'=>'amara.c@email.com','phone'=>'(313) 555-0101','job_category_id'=>1,'source'=>'Indeed','status'=>'hiring','assigned_to'=>$staffIds[0] ?? 1,'resume_text'=>"MSW, University of Michigan. LMSW. 4 years CBT/trauma."],
            ['first_name'=>'David','last_name'=>'Park','email'=>'dpark@email.com','phone'=>'(313) 555-0102','job_category_id'=>2,'source'=>'LinkedIn','status'=>'pre_screening','assigned_to'=>$staffIds[1] ?? 2,'resume_text'=>'MA Counseling Psychology. Youth programs.'],
            ['first_name'=>'Maria','last_name'=>'Santos','email'=>'msantos@email.com','phone'=>'(313) 555-0103','job_category_id'=>3,'source'=>'Referral','status'=>'verification_and_review','assigned_to'=>$staffIds[2] ?? 3,'resume_text'=>'BA Social Work. 2 years direct care.'],
            ['first_name'=>'James','last_name'=>'Wilson','email'=>'jwilson@email.com','phone'=>'(313) 555-0104','job_category_id'=>4,'source'=>'Indeed','status'=>'offer_letter','assigned_to'=>$staffIds[0] ?? 1,'resume_text'=>'5 years care coordination. CCM.'],
            ['first_name'=>'Lisa','last_name'=>'Thompson','email'=>'lthompson@email.com','phone'=>'(313) 555-0105','job_category_id'=>1,'source'=>'Website','status'=>'pre_onboard_documents','assigned_to'=>$staffIds[3] ?? 4,'resume_text'=>'LMSW, 6 years clinical.'],
            ['first_name'=>'Nina','last_name'=>'Rodriguez','email'=>'nrod@email.com','phone'=>'(313) 555-0107','job_category_id'=>3,'source'=>'Indeed','status'=>'hiring','assigned_to'=>$staffIds[2] ?? 3,'resume_text'=>'BA Psychology, 1yr internship.'],
            ['first_name'=>'Alex','last_name'=>'Morgan','email'=>'amorgan@email.com','phone'=>'(313) 555-0108','job_category_id'=>1,'source'=>'Referral','status'=>'verification_and_review','assigned_to'=>$staffIds[0] ?? 1,'resume_text'=>'LPC, substance abuse. 5 years.'],
            ['first_name'=>'Keisha','last_name'=>'Adams','email'=>'kadams@email.com','phone'=>'(313) 555-0121','job_category_id'=>1,'source'=>'Referral','status'=>'hiring','assigned_to'=>$staffIds[3] ?? 4,'resume_text'=>'LCSW, 8 years. Family therapy.'],
            ['first_name'=>'Brian','last_name'=>'Clark','email'=>'bclark@email.com','phone'=>'(313) 555-0120','job_category_id'=>4,'source'=>'Indeed','status'=>'compliance_agreements','assigned_to'=>$staffIds[0] ?? 1,'resume_text'=>'Supports Coordinator. QIDP.'],
        ];
        foreach ($sampleCandidates as $c) {
            Candidate::firstOrCreate(['email' => $c['email']], $c);
        }

        // ── Sample Employees ──
        $sampleEmps = [
            ['first_name'=>'Janet', 'last_name'=>'Williams','email'=>'jwilliams@wbh.com','role'=>'Licensed Clinician','employment_type'=>'Full-Time','department'=>'Clinical',    'start_date'=>'2024-06-15','pay_rate'=>35,'location'=>'Main Office'],
            ['first_name'=>'Kevin', 'last_name'=>'Brown',   'email'=>'kbrown@wbh.com',   'role'=>'Supports Coordinator','employment_type'=>'Full-Time','department'=>'Coordination','start_date'=>'2024-09-01','pay_rate'=>22,'location'=>'Main Office'],
            ['first_name'=>'Ashley','last_name'=>'Martinez','email'=>'amartinez@wbh.com','role'=>'Admin Assistant',   'employment_type'=>'Full-Time','department'=>'Admin',      'start_date'=>'2025-01-10','pay_rate'=>18,'location'=>'Main Office'],
            ['first_name'=>'Monica','last_name'=>'Jackson', 'email'=>'mjackson@wbh.com', 'role'=>'Masters Clinician', 'employment_type'=>'Full-Time','department'=>'Clinical',    'start_date'=>'2024-11-01','pay_rate'=>30,'location'=>'Southfield'],
        ];
        foreach ($sampleEmps as $e) {
            // Check if user already exists
            $user = User::where('email', $e['email'])->first();
            if (!$user) {
                $user = User::create([
                    'first_name' => $e['first_name'],
                    'last_name'  => $e['last_name'],
                    'email'      => $e['email'],
                    'password'   => Hash::make('password'),
                    'role'       => 'employee',
                    'department' => $e['department'],
                    'is_active'  => true,
                ]);
            }

            $emp = Employee::firstOrCreate(
                ['email' => $e['email']],
                array_merge($e, ['is_active' => true, 'user_id' => $user->id])
            );

            // Add sample trainings if not already present
            if (!Training::where('employee_id', $emp->id)->where('name', 'HIPAA Compliance')->exists()) {
                Training::create(['employee_id' => $emp->id, 'name' => 'HIPAA Compliance', 'due_date' => now()->addMonths(6), 'is_completed' => true, 'completed_date' => now()->subMonths(6)]);
            }
            if (!Training::where('employee_id', $emp->id)->where('name', 'CPR/First Aid')->exists()) {
                Training::create(['employee_id' => $emp->id, 'name' => 'CPR/First Aid',    'due_date' => now()->addMonths(3), 'is_completed' => false]);
            }
        }
    }
}
