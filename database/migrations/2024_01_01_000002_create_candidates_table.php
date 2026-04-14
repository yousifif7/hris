<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('candidates', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->foreignId('job_category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('source');                // Indeed, LinkedIn, Referral, Website, Walk-in, Upload
            $table->string('status')->default('needs_review');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->text('resume_text')->nullable();
            $table->string('resume_file')->nullable(); // path to uploaded file
            $table->timestamp('invite_sent_at')->nullable();
            $table->timestamp('last_followup_at')->nullable();
            $table->integer('followup_count')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('assigned_to');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('candidates');
    }
};
