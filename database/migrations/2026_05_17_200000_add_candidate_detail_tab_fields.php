<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Fields backing the candidate detail page tabs (Hiring/Pre-Screening/Pre-Interview Questions)
     * and the right-side metadata column.
     */
    public function up(): void
    {
        Schema::table('candidates', function (Blueprint $table) {
            // Pre-Screening tab
            if (! Schema::hasColumn('candidates', 'candidate_for')) {
                $table->string('candidate_for')->nullable();
            }
            if (! Schema::hasColumn('candidates', 'resume_w_applicable_experience')) {
                $table->text('resume_w_applicable_experience')->nullable();
            }
            if (! Schema::hasColumn('candidates', 'pre_screen_note')) {
                $table->text('pre_screen_note')->nullable();
            }
            if (! Schema::hasColumn('candidates', 'pre_screening_status')) {
                $table->string('pre_screening_status')->nullable();
            }

            // Pre-Interview Questions tab
            if (! Schema::hasColumn('candidates', 'full_or_part_time')) {
                $table->string('full_or_part_time')->nullable();
            }
            if (! Schema::hasColumn('candidates', 'ideal_schedule')) {
                $table->json('ideal_schedule')->nullable();
            }
            if (! Schema::hasColumn('candidates', 'description')) {
                $table->text('description')->nullable();
            }
            if (! Schema::hasColumn('candidates', 'days_available')) {
                $table->json('days_available')->nullable();
            }
            if (! Schema::hasColumn('candidates', 'position')) {
                $table->string('position')->nullable();
            }
            if (! Schema::hasColumn('candidates', 'clinical_position_type')) {
                $table->string('clinical_position_type')->nullable();
            }
            if (! Schema::hasColumn('candidates', 'position_type')) {
                $table->string('position_type')->nullable();
            }
            if (! Schema::hasColumn('candidates', 'staff_type')) {
                $table->string('staff_type')->nullable();
            }
            if (! Schema::hasColumn('candidates', 'signed_application_path')) {
                $table->string('signed_application_path')->nullable();
            }
            if (! Schema::hasColumn('candidates', 'signed_application_name')) {
                $table->string('signed_application_name')->nullable();
            }

            // Right-sidebar expirations / counters
            if (! Schema::hasColumn('candidates', 'background_check_expires_at')) {
                $table->date('background_check_expires_at')->nullable();
            }
            if (! Schema::hasColumn('candidates', 'cpr_certification_expires_at')) {
                $table->date('cpr_certification_expires_at')->nullable();
            }
            if (! Schema::hasColumn('candidates', 'tb_expires_at')) {
                $table->date('tb_expires_at')->nullable();
            }
            if (! Schema::hasColumn('candidates', 'pgl_insurance_expires_at')) {
                $table->date('pgl_insurance_expires_at')->nullable();
            }
            if (! Schema::hasColumn('candidates', 'cmhp_hours_current_year')) {
                $table->decimal('cmhp_hours_current_year', 8, 2)->nullable();
            }
            if (! Schema::hasColumn('candidates', 'dwc_training_progress')) {
                $table->unsignedTinyInteger('dwc_training_progress')->default(0);
            }
        });
    }

    public function down(): void
    {
        Schema::table('candidates', function (Blueprint $table) {
            $cols = [
                'candidate_for', 'resume_w_applicable_experience', 'pre_screen_note', 'pre_screening_status',
                'full_or_part_time', 'ideal_schedule', 'description', 'days_available',
                'position', 'clinical_position_type', 'position_type', 'staff_type',
                'signed_application_path', 'signed_application_name',
                'background_check_expires_at', 'cpr_certification_expires_at', 'tb_expires_at',
                'pgl_insurance_expires_at', 'cmhp_hours_current_year', 'dwc_training_progress',
            ];
            foreach ($cols as $c) {
                if (Schema::hasColumn('candidates', $c)) {
                    $table->dropColumn($c);
                }
            }
        });
    }
};
