<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $taskMap = [
            'Pre-interview Questions' => 'Upload Signed Offer Letter',
            'Verifications and Review' => 'Complete Background Check Consent',
            'Pre-Onboarding Documents' => 'Submit References',
            'Compliance Agreements' => 'Upload Credentials & Licenses',
            'Clinical Staff Document' => 'Complete I-9 Verification',
            'Emergency Contact' => 'Upload Driver\'s License',
            'Training and Development' => 'Select Orientation Date',
            'Financial and Payroll Information' => 'Setup Email Account',
            'Post-offer Documents' => 'Building Access & WiFi',
            'DWC Training' => 'Review Employee Handbook',
            'Additional' => 'Complete Initial Training Modules',
        ];

        foreach ($taskMap as $oldName => $newName) {
            DB::table('onboarding_templates')
                ->where('name', $oldName)
                ->update(['name' => $newName, 'updated_at' => now()]);

            DB::table('onboarding_tasks')
                ->where('task_name', $oldName)
                ->update(['task_name' => $newName, 'updated_at' => now()]);
        }

        foreach (array_values($taskMap) as $index => $name) {
            DB::table('onboarding_templates')->updateOrInsert(
                ['name' => $name],
                [
                    'sort_order' => $index,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }

    public function down(): void
    {
        $taskMap = [
            'Pre-interview Questions' => 'Upload Signed Offer Letter',
            'Verifications and Review' => 'Complete Background Check Consent',
            'Pre-Onboarding Documents' => 'Submit References',
            'Compliance Agreements' => 'Upload Credentials & Licenses',
            'Clinical Staff Document' => 'Complete I-9 Verification',
            'Emergency Contact' => 'Upload Driver\'s License',
            'Training and Development' => 'Select Orientation Date',
            'Financial and Payroll Information' => 'Setup Email Account',
            'Post-offer Documents' => 'Building Access & WiFi',
            'DWC Training' => 'Review Employee Handbook',
            'Additional' => 'Complete Initial Training Modules',
        ];

        foreach ($taskMap as $oldName => $newName) {
            DB::table('onboarding_templates')
                ->where('name', $newName)
                ->update(['name' => $oldName, 'updated_at' => now()]);

            DB::table('onboarding_tasks')
                ->where('task_name', $newName)
                ->update(['task_name' => $oldName, 'updated_at' => now()]);
        }
    }
};