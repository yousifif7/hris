<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('candidate_tasks')) {
            return;
        }

        Schema::create('candidate_tasks', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->foreignId('candidate_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assigned_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->string('status')->default('need_status_update');
            $table->dateTime('evaluation_date_time')->nullable();
            $table->string('review_records')->nullable();
            $table->string('was_written_verbal_consent_obtained')->nullable();
            $table->string('did_the_consumer_have_autism')->nullable();
            $table->text('description')->nullable();

            $table->string('teams')->nullable();

            // Quality review
            $table->text('quality_review')->nullable();
            $table->text('quality_assurance')->nullable();

            // Clinical Consultation
            $table->text('report_review_status')->nullable();
            $table->text('reviewer')->nullable();
            $table->text('supervisor_review')->nullable();
            $table->text('signed_report')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('candidate_tasks');
    }
};
