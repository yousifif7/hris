<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('candidate_activities')) {
            return;
        }

        Schema::create('candidate_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('assigned_user_id')->nullable()->constrained('users')->nullOnDelete();

            // kind: 'scheduled' (Activities card) or 'logged' (History card)
            $table->string('kind', 16)->index();
            // type: meeting, call, email, note, due_date, supervision_reminder, re_evaluation
            $table->string('type', 32)->index();

            $table->string('subject', 255)->nullable();
            $table->text('description')->nullable();
            $table->dateTime('scheduled_at')->nullable();
            $table->dateTime('occurred_at')->nullable();

            $table->timestamps();

            $table->index(['candidate_id', 'kind']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('candidate_activities');
    }
};
