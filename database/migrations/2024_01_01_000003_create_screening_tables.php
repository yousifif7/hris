<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Pre-screening questionnaire responses
        Schema::create('pre_screenings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_id')->constrained()->cascadeOnDelete();
            $table->string('education_level');         // High School, Associates, Bachelors, Masters, Doctorate
            $table->integer('years_experience')->default(0);
            $table->string('licenses')->nullable();    // comma-separated: LMSW, LPC, etc.
            $table->string('availability');             // Full-Time, Part-Time, Either, 1099
            $table->date('earliest_start_date')->nullable();
            $table->text('additional_notes')->nullable();
            $table->foreignId('screened_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // Interviews
        Schema::create('interviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_id')->constrained()->cascadeOnDelete();
            $table->foreignId('interviewer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('scheduled_at');
            $table->integer('duration_minutes')->default(20);
            $table->string('type')->default('zoom');   // zoom, in-person, phone
            $table->string('meeting_link')->nullable();
            $table->enum('status', ['scheduled', 'completed', 'cancelled', 'no_show'])->default('scheduled');
            $table->text('notes')->nullable();          // Interviewer's notes
            $table->json('question_responses')->nullable(); // structured Q&A
            $table->timestamps();
        });

        // Background checks
        Schema::create('background_checks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_id')->constrained()->cascadeOnDelete();
            $table->string('check_type');               // mdhhs, sam_oig, npdb, application, bg_consent
            $table->enum('status', ['pending', 'in_progress', 'complete', 'failed'])->default('pending');
            $table->text('notes')->nullable();
            $table->date('completed_at')->nullable();
            $table->timestamps();
        });

        // References
        Schema::create('candidate_references', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_id')->constrained()->cascadeOnDelete();
            $table->string('reference_name');
            $table->string('reference_email');
            $table->string('reference_phone')->nullable();
            $table->string('relationship')->nullable(); // supervisor, colleague, etc.
            $table->enum('status', ['pending', 'sent', 'received'])->default('pending');
            $table->text('questions_sent')->nullable();
            $table->text('response')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('candidate_references');
        Schema::dropIfExists('background_checks');
        Schema::dropIfExists('interviews');
        Schema::dropIfExists('pre_screenings');
    }
};
