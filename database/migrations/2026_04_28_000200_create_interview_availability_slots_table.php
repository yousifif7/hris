<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('interview_availability_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_id')->constrained()->cascadeOnDelete();
            $table->dateTime('starts_at');
            $table->dateTime('ends_at');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('booked_interview_id')->nullable()->constrained('interviews')->nullOnDelete();
            $table->timestamps();

            $table->index(['candidate_id', 'starts_at']);
            $table->index('booked_interview_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('interview_availability_slots');
    }
};
