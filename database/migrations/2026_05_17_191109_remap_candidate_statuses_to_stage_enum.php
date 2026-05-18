<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Collapse the old fine-grained candidate workflow statuses into the new
     * stage-oriented enum used by the sidebar (Hiring → ... → Job Description Letter).
     *
     * The string-column default itself is already updated in the original create_candidates
     * migration; fresh installs land on 'hiring'. Existing databases only need the row remap.
     */
    public function up(): void
    {
        $map = [
            'needs_review'              => 'hiring',
            'queue'                     => 'hiring',
            'invite_sent'               => 'pre_screening',
            'no_response'               => 'pre_screening',
            'interview_scheduled'       => 'pre_screening',
            'post_interview_review'     => 'pre_interview_questions',
            'pre_screening_passed'      => 'verification_and_review',
            'awaiting_background_check' => 'verification_and_review',
            'offer_sent'                => 'offer_letter',
            'offer_accepted'            => 'pre_onboard_documents',
            'onboarding'                => 'pre_onboard_documents',
        ];

        foreach ($map as $old => $new) {
            DB::table('candidates')->where('status', $old)->update(['status' => $new]);
        }
    }

    public function down(): void
    {
        // Best-effort reverse mapping. The original old-status granularity is unrecoverable
        // (multiple old statuses now share a single new value), so we settle for the closest
        // canonical pre-migration value.
        $reverse = [
            'hiring'                  => 'needs_review',
            'pre_screening'           => 'invite_sent',
            'pre_interview_questions' => 'post_interview_review',
            'verification_and_review' => 'pre_screening_passed',
            'offer_letter'            => 'offer_sent',
            'pre_onboard_documents'   => 'offer_accepted',
        ];

        foreach ($reverse as $new => $old) {
            DB::table('candidates')->where('status', $new)->update(['status' => $old]);
        }
    }
};
