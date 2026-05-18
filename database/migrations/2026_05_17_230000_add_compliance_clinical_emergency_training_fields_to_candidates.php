<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adds columns backing the Compliance Agreements, Clinical Staff Documents,
     * Emergency Contact, and Training and Development tabs.
     */
    public function up(): void
    {
        Schema::table('candidates', function (Blueprint $table) {
            // Compliance Agreements
            if (! Schema::hasColumn('candidates', 'baa_agreement')) {
                $table->string('baa_agreement')->nullable();
            }
            if (! Schema::hasColumn('candidates', 'nda_hipaa')) {
                $table->string('nda_hipaa')->nullable();
            }
            if (! Schema::hasColumn('candidates', 'acknowledgement_handbook')) {
                $table->boolean('acknowledgement_handbook')->default(false);
            }

            // Clinical Staff Documents
            if (! Schema::hasColumn('candidates', 'professional_general_liability_insurance')) {
                $table->string('professional_general_liability_insurance')->nullable();
            }
            if (! Schema::hasColumn('candidates', 'clinical_licenses')) {
                $table->string('clinical_licenses')->nullable();
            }
            if (! Schema::hasColumn('candidates', 'medversant_application_confirmation')) {
                $table->string('medversant_application_confirmation')->nullable();
            }
            if (! Schema::hasColumn('candidates', 'writing_sample')) {
                $table->string('writing_sample')->nullable();
            }

            // Emergency Contact
            if (! Schema::hasColumn('candidates', 'emergency_contact_1_name')) {
                $table->string('emergency_contact_1_name')->nullable();
            }
            if (! Schema::hasColumn('candidates', 'emergency_contact_1_phone')) {
                $table->string('emergency_contact_1_phone')->nullable();
            }
            if (! Schema::hasColumn('candidates', 'emergency_contact_2_name')) {
                $table->string('emergency_contact_2_name')->nullable();
            }
            if (! Schema::hasColumn('candidates', 'emergency_contact_2_phone')) {
                $table->string('emergency_contact_2_phone')->nullable();
            }

            // Training and Development
            if (! Schema::hasColumn('candidates', 'recipient_rights_training_path')) {
                $table->string('recipient_rights_training_path')->nullable();
            }
            if (! Schema::hasColumn('candidates', 'recipient_rights_training_name')) {
                $table->string('recipient_rights_training_name')->nullable();
            }
            if (! Schema::hasColumn('candidates', 'recipient_rights_training_expires_at')) {
                $table->date('recipient_rights_training_expires_at')->nullable();
            }
            if (! Schema::hasColumn('candidates', 'handbook')) {
                $table->string('handbook')->nullable();
            }
            if (! Schema::hasColumn('candidates', 'annual_ceus_path')) {
                $table->string('annual_ceus_path')->nullable();
            }
            if (! Schema::hasColumn('candidates', 'annual_ceus_name')) {
                $table->string('annual_ceus_name')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('candidates', function (Blueprint $table) {
            $cols = [
                'baa_agreement', 'nda_hipaa', 'acknowledgement_handbook',
                'professional_general_liability_insurance', 'clinical_licenses',
                'medversant_application_confirmation', 'writing_sample',
                'emergency_contact_1_name', 'emergency_contact_1_phone',
                'emergency_contact_2_name', 'emergency_contact_2_phone',
                'recipient_rights_training_path', 'recipient_rights_training_name',
                'recipient_rights_training_expires_at', 'handbook',
                'annual_ceus_path', 'annual_ceus_name',
            ];
            foreach ($cols as $c) {
                if (Schema::hasColumn('candidates', $c)) {
                    $table->dropColumn($c);
                }
            }
        });
    }
};
