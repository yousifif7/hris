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
        ];
        foreach ($settings as $k => $v) Setting::create(['key' => $k, 'value' => $v]);

        // ── HR Staff ──
        $staff = [
            ['first_name' => 'Admin',   'last_name' => 'User',    'email' => 'admin@hris.com',   'role' => 'admin',    'round_robin_order' => 1],
            ['first_name' => 'Sarah',   'last_name' => 'Johnson',  'email' => 'sjohnson@hris.com', 'role' => 'hr_staff', 'round_robin_order' => 2],
            ['first_name' => 'Marcus',  'last_name' => 'Lee',      'email' => 'mlee@hris.com',     'role' => 'hr_staff', 'round_robin_order' => 3],
            ['first_name' => 'Denise',  'last_name' => 'Harris',   'email' => 'dharris@hris.com',  'role' => 'hr_staff', 'round_robin_order' => 4],
        ];
        foreach ($staff as $s) {
            User::create(array_merge($s, ['password' => Hash::make('password'), 'is_active' => true]));
        }

        // ── Job Categories ──
        $cats = ['Licensed Clinician', 'Masters Level', 'Bachelors Level', 'Supports Coordinator', 'Administrative'];
        foreach ($cats as $c) {
            JobCategory::create(['name' => $c, 'slug' => \Str::slug($c)]);
        }

        // ── Onboarding Templates ──
        $onbSteps = [
            'Upload signed offer letter', 'Complete background check consent', 'Submit references',
            'Upload credentials & licenses', 'Complete I-9 verification', "Upload driver's license",
            'Select orientation date', 'Setup email account', 'Building access & WiFi credentials',
            'Review employee handbook', 'Complete initial training modules',
        ];
        foreach ($onbSteps as $i => $name) {
            OnboardingTemplate::create(['name' => $name, 'sort_order' => $i]);
        }

        // ── Email Templates ──
        $templates = [
            ['slug' => 'invite',              'name' => 'Interview Invite',          'subject' => 'Interview Invitation — {{company_name}}',       'body' => "Dear {{candidate_name}},\n\nThank you for your interest in the {{role}} position at {{company_name}}.\n\nPlease select a time: {{scheduling_link}}\n\nThe interview is 15-20 min via Zoom.\n\nBest regards,\n{{hr_name}}"],
            ['slug' => 'followup',            'name' => 'No Response Follow-up',     'subject' => 'Following Up — {{company_name}}',               'body' => "Hi {{candidate_name}},\n\nWe recently reached out about the {{role}} position. Still interested?\n\nSchedule here: {{scheduling_link}}\n\nBest,\n{{hr_name}}"],
            ['slug' => 'reject',              'name' => 'Rejection',                 'subject' => 'Application Update — {{company_name}}',         'body' => "Dear {{candidate_name}},\n\nThank you for your interest. After review, we've moved forward with other candidates.\n\nBest wishes,\n{{hr_name}}"],
            ['slug' => 'prescreening',        'name' => 'Pre-Screening Next Steps',  'subject' => 'Next Steps — {{company_name}}',                 'body' => "Dear {{candidate_name}},\n\nCongratulations on advancing for {{role}}!\n\nPlease complete:\n1. Full Application\n2. Background Check Consent\n3. Reference Submission (3 refs)\n\nBest,\n{{hr_name}}"],
            ['slug' => 'reference',           'name' => 'Reference Request',         'subject' => 'Reference Request for {{candidate_name}}',      'body' => "Dear {{reference_name}},\n\n{{candidate_name}} listed you as a reference at {{company_name}}.\n\n1. How long have you known them?\n2. What capacity?\n3. Strengths?\n4. Recommend?\n\nThank you,\n{{hr_name}}"],
            ['slug' => 'offer',               'name' => 'Offer Letter',              'subject' => 'Offer Letter — {{company_name}}',               'body' => "Dear {{candidate_name}},\n\nWe're pleased to offer you {{role}} at {{company_name}}.\n\nPay: {{pay_rate}}\nType: {{employment_type}}\nLocation: {{location}}\nStart: {{start_date}}\n\nPlease respond within 20 days.\n\n{{hr_name}}"],
            ['slug' => 'onboarding',          'name' => 'Onboarding Welcome',        'subject' => 'Welcome to {{company_name}}!',                  'body' => "Dear {{candidate_name}},\n\nWelcome! Here's what you need:\n- Email login info\n- WiFi credentials\n- Building access: front desk day 1\n\nComplete all onboarding tasks in your portal.\n\nBest,\n{{hr_name}}"],
            ['slug' => 'interview_confirmation', 'name' => 'Interview Confirmation', 'subject' => 'Interview Confirmed — {{company_name}}',       'body' => "Dear {{candidate_name}},\n\nYour interview for {{role}} is confirmed.\n\nDetails will be sent shortly.\n\nBest,\n{{hr_name}}"],
            ['slug' => 'declined_followup',   'name' => 'Declined Follow-up',        'subject' => 'Thank You — {{company_name}}',                  'body' => "Dear {{candidate_name}},\n\nWe understand your decision. We'd love to stay in touch for future opportunities.\n\nBest,\n{{hr_name}}"],
            ['slug' => 'sms_followup',            'name' => 'SMS Follow-up (No Response)',  'subject' => '',                                              'body' => "Hi {{candidate_first_name}}, this is {{hr_name}} from {{company_name}}. Still interested in the {{role}} position? Book a quick interview here: {{scheduling_link}}", 'category' => 'sms'],
            ['slug' => 'portal_credentials',  'name' => 'Portal Credentials',        'subject' => 'Your {{company_name}} Employee Portal Access',   'body' => "Dear {{candidate_name}},\n\nCongratulations and welcome to {{company_name}}!\n\nYour employee portal access has been created:\n\nLogin URL:          {{login_url}}\nEmail:              {{login_email}}\nTemporary Password: {{temp_password}}\n\nBuilding Access: {{door_code}}\nWiFi Password:   {{wifi_password}}\n\nPlease log in and change your password on first sign-in.\n\nBest regards,\n{{hr_name}}"],
        ];
        foreach ($templates as $t) EmailTemplate::create($t);

        // ── Automation Rules ──
        $rules = [
            ['trigger_event' => 'candidate_created',  'action_type' => 'notify',     'action_config' => ['target' => 'assigned_hr']],
            ['trigger_event' => 'status_changed',     'trigger_value' => 'invite_sent',             'action_type' => 'send_email', 'action_config' => ['template' => 'invite']],
            ['trigger_event' => 'no_response',        'trigger_value' => '5_days',                  'action_type' => 'send_sms',   'action_config' => ['template' => 'sms_followup'], 'delay_hours' => 120],
            ['trigger_event' => 'no_response',        'trigger_value' => '10_days',                 'action_type' => 'move_to_queue', 'delay_hours' => 240],
            ['trigger_event' => 'status_changed',     'trigger_value' => 'interview_scheduled',     'action_type' => 'send_email', 'action_config' => ['template' => 'interview_confirmation']],
            ['trigger_event' => 'status_changed',     'trigger_value' => 'pre_screening_passed',    'action_type' => 'send_email', 'action_config' => ['template' => 'prescreening'], 'delay_hours' => 48],
            ['trigger_event' => 'reference_submitted','action_type' => 'send_email', 'action_config' => ['template' => 'reference']],
            ['trigger_event' => 'bg_checks_complete', 'action_type' => 'notify',     'action_config' => ['target' => 'assigned_hr']],
            ['trigger_event' => 'status_changed',     'trigger_value' => 'offer_sent',              'action_type' => 'send_email', 'action_config' => ['template' => 'offer']],
            ['trigger_event' => 'status_changed',     'trigger_value' => 'offer_accepted',          'action_type' => 'send_email', 'action_config' => ['template' => 'onboarding']],
            ['trigger_event' => 'status_changed',     'trigger_value' => 'rejected',                'action_type' => 'send_email', 'action_config' => ['template' => 'reject']],
            ['trigger_event' => 'status_changed',     'trigger_value' => 'applicant_declined',      'action_type' => 'send_email', 'action_config' => ['template' => 'declined_followup']],
            ['trigger_event' => 'training_expiring',  'action_type' => 'notify',     'action_config' => ['days_before' => 30]],
        ];
        foreach ($rules as $r) AutomationRule::create(array_merge(['is_active' => true], $r));

        // ── Sample Candidates ──
        $sampleCandidates = [
            ['first_name'=>'Amara','last_name'=>'Chen','email'=>'amara.c@email.com','phone'=>'(313) 555-0101','job_category_id'=>1,'source'=>'Indeed','status'=>'needs_review','assigned_to'=>1,'resume_text'=>"MSW, University of Michigan. LMSW. 4 years CBT/trauma."],
            ['first_name'=>'David','last_name'=>'Park','email'=>'dpark@email.com','phone'=>'(313) 555-0102','job_category_id'=>2,'source'=>'LinkedIn','status'=>'interview_scheduled','assigned_to'=>2,'resume_text'=>'MA Counseling Psychology. Youth programs.'],
            ['first_name'=>'Maria','last_name'=>'Santos','email'=>'msantos@email.com','phone'=>'(313) 555-0103','job_category_id'=>3,'source'=>'Referral','status'=>'pre_screening_passed','assigned_to'=>3,'resume_text'=>'BA Social Work. 2 years direct care.'],
            ['first_name'=>'James','last_name'=>'Wilson','email'=>'jwilson@email.com','phone'=>'(313) 555-0104','job_category_id'=>4,'source'=>'Indeed','status'=>'offer_sent','assigned_to'=>1,'resume_text'=>'5 years care coordination. CCM.'],
            ['first_name'=>'Lisa','last_name'=>'Thompson','email'=>'lthompson@email.com','phone'=>'(313) 555-0105','job_category_id'=>1,'source'=>'Website','status'=>'offer_accepted','assigned_to'=>4,'resume_text'=>'LMSW, 6 years clinical.'],
            ['first_name'=>'Nina','last_name'=>'Rodriguez','email'=>'nrod@email.com','phone'=>'(313) 555-0107','job_category_id'=>3,'source'=>'Indeed','status'=>'needs_review','assigned_to'=>3,'resume_text'=>'BA Psychology, 1yr internship.'],
            ['first_name'=>'Alex','last_name'=>'Morgan','email'=>'amorgan@email.com','phone'=>'(313) 555-0108','job_category_id'=>1,'source'=>'Referral','status'=>'awaiting_background_check','assigned_to'=>1,'resume_text'=>'LPC, substance abuse. 5 years.'],
            ['first_name'=>'Keisha','last_name'=>'Adams','email'=>'kadams@email.com','phone'=>'(313) 555-0121','job_category_id'=>1,'source'=>'Referral','status'=>'needs_review','assigned_to'=>4,'resume_text'=>'LCSW, 8 years. Family therapy.'],
            ['first_name'=>'Brian','last_name'=>'Clark','email'=>'bclark@email.com','phone'=>'(313) 555-0120','job_category_id'=>4,'source'=>'Indeed','status'=>'onboarding','assigned_to'=>1,'resume_text'=>'Supports Coordinator. QIDP.'],
        ];
        foreach ($sampleCandidates as $c) Candidate::create($c);

        // ── Sample Employees ──
        $sampleEmps = [
            ['first_name'=>'Janet', 'last_name'=>'Williams','email'=>'jwilliams@wbh.com','role'=>'Licensed Clinician','employment_type'=>'Full-Time','department'=>'Clinical',    'start_date'=>'2024-06-15','pay_rate'=>35,'location'=>'Main Office'],
            ['first_name'=>'Kevin', 'last_name'=>'Brown',   'email'=>'kbrown@wbh.com',   'role'=>'Supports Coordinator','employment_type'=>'Full-Time','department'=>'Coordination','start_date'=>'2024-09-01','pay_rate'=>22,'location'=>'Main Office'],
            ['first_name'=>'Ashley','last_name'=>'Martinez','email'=>'amartinez@wbh.com','role'=>'Admin Assistant',   'employment_type'=>'Full-Time','department'=>'Admin',      'start_date'=>'2025-01-10','pay_rate'=>18,'location'=>'Main Office'],
            ['first_name'=>'Monica','last_name'=>'Jackson', 'email'=>'mjackson@wbh.com', 'role'=>'Masters Clinician', 'employment_type'=>'Full-Time','department'=>'Clinical',    'start_date'=>'2024-11-01','pay_rate'=>30,'location'=>'Southfield'],
        ];
        foreach ($sampleEmps as $e) {
            // Create a user account for the employee
            $user = User::create([
                'first_name' => $e['first_name'],
                'last_name'  => $e['last_name'],
                'email'      => $e['email'],
                'password'   => Hash::make('password'),
                'role'       => 'employee',
                'department' => $e['department'],
                'is_active'  => true,
            ]);

            $emp = Employee::create(array_merge($e, ['is_active' => true, 'user_id' => $user->id]));

            // Add sample trainings
            Training::create(['employee_id' => $emp->id, 'name' => 'HIPAA Compliance', 'due_date' => now()->addMonths(6), 'is_completed' => true, 'completed_date' => now()->subMonths(6)]);
            Training::create(['employee_id' => $emp->id, 'name' => 'CPR/First Aid',    'due_date' => now()->addMonths(3), 'is_completed' => false]);
        }
    }
}
