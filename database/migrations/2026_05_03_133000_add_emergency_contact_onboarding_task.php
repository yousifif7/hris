<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $taskName = 'Collect Emergency Contact Details';
        $now = now();

        DB::table('onboarding_templates')->updateOrInsert(
            ['name' => $taskName],
            [
                'sort_order' => 6,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        $candidateIds = DB::table('candidates')
            ->whereIn('status', ['offer_accepted', 'onboarding'])
            ->pluck('id');

        foreach ($candidateIds as $candidateId) {
            $exists = DB::table('onboarding_tasks')
                ->where('candidate_id', $candidateId)
                ->where('task_name', $taskName)
                ->exists();

            if ($exists) {
                continue;
            }

            $maxSort = (int) (DB::table('onboarding_tasks')->where('candidate_id', $candidateId)->max('sort_order') ?? 0);

            DB::table('onboarding_tasks')->insert([
                'candidate_id' => $candidateId,
                'template_id' => null,
                'task_name' => $taskName,
                'is_completed' => false,
                'completed_at' => null,
                'document_path' => null,
                'sort_order' => $maxSort + 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        $taskName = 'Collect Emergency Contact Details';
        DB::table('onboarding_tasks')->where('task_name', $taskName)->delete();
        DB::table('onboarding_templates')->where('name', $taskName)->delete();
    }
};