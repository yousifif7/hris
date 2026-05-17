<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adds columns backing the Verification & Review, Offer Letter, and
     * Pre-Onboard Documents tabs on the candidate detail page.
     */
    public function up(): void
    {
        Schema::table('candidates', function (Blueprint $table) {
            // Verification & Review
            if (! Schema::hasColumn('candidates', 'background_check_status')) {
                $table->string('background_check_status')->nullable();
            }
            if (! Schema::hasColumn('candidates', 'identification_expires_at')) {
                $table->date('identification_expires_at')->nullable();
            }
            if (! Schema::hasColumn('candidates', 'i9_verification')) {
                $table->json('i9_verification')->nullable();
            }
            if (! Schema::hasColumn('candidates', 'onboarding_documents_checklist')) {
                $table->json('onboarding_documents_checklist')->nullable();
            }
            foreach (['1', '2'] as $n) {
                foreach (['name', 'phone', 'association'] as $part) {
                    $col = "reference_{$n}_{$part}";
                    if (! Schema::hasColumn('candidates', $col)) {
                        $table->string($col)->nullable();
                    }
                }
            }

            // Offer Letter
            if (! Schema::hasColumn('candidates', 'offer_date')) {
                $table->date('offer_date')->nullable();
            }
            if (! Schema::hasColumn('candidates', 'offer_mccrory_center')) {
                $table->string('offer_mccrory_center')->nullable();
            }
            if (! Schema::hasColumn('candidates', 'operations_manager')) {
                $table->string('operations_manager')->nullable();
            }
            if (! Schema::hasColumn('candidates', 'clinical_supervisor')) {
                $table->string('clinical_supervisor')->nullable();
            }
            if (! Schema::hasColumn('candidates', 'offer_amount')) {
                $table->decimal('offer_amount', 10, 2)->nullable();
            }
            if (! Schema::hasColumn('candidates', 'payment_frequency')) {
                $table->string('payment_frequency')->nullable();
            }
            if (! Schema::hasColumn('candidates', 'company_representative')) {
                $table->string('company_representative')->nullable();
            }
            if (! Schema::hasColumn('candidates', 'offer_deadline_date')) {
                $table->date('offer_deadline_date')->nullable();
            }

            // Pre-Onboard Documents
            if (! Schema::hasColumn('candidates', 'college_degree')) {
                $table->string('college_degree')->nullable();
            }
            if (! Schema::hasColumn('candidates', 'college_transcripts')) {
                $table->string('college_transcripts')->nullable();
            }
            if (! Schema::hasColumn('candidates', 'cpr_certification')) {
                $table->string('cpr_certification')->nullable();
            }
            if (! Schema::hasColumn('candidates', 'child_registry_clearance')) {
                $table->string('child_registry_clearance')->nullable();
            }
            if (! Schema::hasColumn('candidates', 'child_registry_clearance_expires_at')) {
                $table->date('child_registry_clearance_expires_at')->nullable();
            }
            if (! Schema::hasColumn('candidates', 'tb_test_results')) {
                $table->string('tb_test_results')->nullable();
            }
            if (! Schema::hasColumn('candidates', 'dwihn_transcripts')) {
                $table->string('dwihn_transcripts')->nullable();
            }
            if (! Schema::hasColumn('candidates', 'i9_document')) {
                $table->string('i9_document')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('candidates', function (Blueprint $table) {
            $cols = [
                'background_check_status', 'identification_expires_at',
                'i9_verification', 'onboarding_documents_checklist',
                'reference_1_name', 'reference_1_phone', 'reference_1_association',
                'reference_2_name', 'reference_2_phone', 'reference_2_association',
                'offer_date', 'offer_mccrory_center', 'operations_manager',
                'clinical_supervisor', 'offer_amount', 'payment_frequency',
                'company_representative', 'offer_deadline_date',
                'college_degree', 'college_transcripts', 'cpr_certification',
                'child_registry_clearance', 'child_registry_clearance_expires_at',
                'tb_test_results', 'dwihn_transcripts', 'i9_document',
            ];
            foreach ($cols as $c) {
                if (Schema::hasColumn('candidates', $c)) {
                    $table->dropColumn($c);
                }
            }
        });
    }
};
