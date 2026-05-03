<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $legacyStageTaskNames = [
            'Pre-Screening',
            'Pre-interview Questions',
            'Pre-Interview Questions',
            'Verification and Review',
            'Verifications and Review',
            'Offer Letter',
            'Pre-Onboarding Documents',
            'Compliance Agreements',
            'Clinical Staff Document',
            'Clinical Staff Documents',
            'Emergency Contact',
            'Training and Development',
            'Financial and Payroll Information',
            'Post-offer Documents',
            'Post-Offer Documents',
            'DWC Training',
            'DWC Trainings',
            'Additional',
        ];

        DB::table('onboarding_tasks')
            ->whereIn('task_name', $legacyStageTaskNames)
            ->delete();

        // Keep templates clean too: these labels are workflow page titles, not onboarding tasks.
        DB::table('onboarding_templates')
            ->whereIn('name', $legacyStageTaskNames)
            ->delete();

        // Deduplicate tasks per candidate + task_name, preferring completed entries.
        $groups = DB::table('onboarding_tasks')
            ->select('candidate_id', 'task_name', DB::raw('COUNT(*) as total'))
            ->groupBy('candidate_id', 'task_name')
            ->having('total', '>', 1)
            ->get();

        foreach ($groups as $group) {
            $rows = DB::table('onboarding_tasks')
                ->where('candidate_id', $group->candidate_id)
                ->where('task_name', $group->task_name)
                ->orderByDesc('is_completed')
                ->orderByDesc('completed_at')
                ->orderByDesc('updated_at')
                ->orderByDesc('id')
                ->get(['id']);

            $keepId = $rows->first()?->id;
            if (! $keepId) {
                continue;
            }

            DB::table('onboarding_tasks')
                ->where('candidate_id', $group->candidate_id)
                ->where('task_name', $group->task_name)
                ->where('id', '!=', $keepId)
                ->delete();
        }
    }

    public function down(): void
    {
        // Non-reversible cleanup migration.
    }
};
