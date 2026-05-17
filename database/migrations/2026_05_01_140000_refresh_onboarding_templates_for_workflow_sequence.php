<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $templates = [
            'Pre-interview Questions',
            'Verifications and Review',
            'Pre-Onboarding Documents',
            'Compliance Agreements',
            'Clinical Staff Document',
            'Emergency Contact',
            'Training and Development',
            'Financial and Payroll Information',
            'Post-offer Documents',
            'DWC Training',
            'Additional',
        ];

        $now = now();

        foreach ($templates as $index => $name) {
            DB::table('onboarding_templates')->updateOrInsert(
                ['name' => $name],
                [
                    'sort_order' => $index,
                    'is_active' => true,
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );
        }

        $candidates = DB::table('candidates')
            ->whereIn('status', ['onboarding', 'offer_accepted'])
            ->pluck('id');

        foreach ($candidates as $candidateId) {
            $existingNames = DB::table('onboarding_tasks')
                ->where('candidate_id', $candidateId)
                ->pluck('task_name')
                ->map(fn ($n) => mb_strtolower((string) $n))
                ->all();

            $maxSort = (int) (DB::table('onboarding_tasks')->where('candidate_id', $candidateId)->max('sort_order') ?? 0);

            foreach ($templates as $templateName) {
                if (in_array(mb_strtolower($templateName), $existingNames, true)) {
                    continue;
                }

                $maxSort++;
                DB::table('onboarding_tasks')->insert([
                    'candidate_id' => $candidateId,
                    'template_id' => null,
                    'task_name' => $templateName,
                    'is_completed' => false,
                    'completed_at' => null,
                    'document_path' => null,
                    'sort_order' => $maxSort,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    public function down(): void
    {
        $templateNames = [
            'Pre-interview Questions',
            'Verifications and Review',
            'Pre-Onboarding Documents',
            'Compliance Agreements',
            'Clinical Staff Document',
            'Emergency Contact',
            'Training and Development',
            'Financial and Payroll Information',
            'Post-offer Documents',
            'DWC Training',
            'Additional',
        ];

        DB::table('onboarding_templates')->whereIn('name', $templateNames)->delete();
        DB::table('onboarding_tasks')->whereIn('task_name', $templateNames)->delete();
    }
};
