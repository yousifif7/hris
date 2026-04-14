<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Offer letters
        if (! Schema::hasTable('offers')) {
            Schema::create('offers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('candidate_id')->constrained()->cascadeOnDelete();
                $table->decimal('pay_rate', 10, 2);
                $table->string('pay_type')->default('hourly');   // hourly, salary
                $table->string('employment_type');                // Full-Time, Part-Time, 1099
                $table->string('location')->nullable();
                $table->text('required_documents')->nullable();
                $table->integer('deadline_days')->default(20);
                $table->date('orientation_date')->nullable();
                $table->date('start_date')->nullable();
                $table->enum('status', ['draft', 'sent', 'viewed', 'accepted', 'declined', 'expired'])->default('draft');
                $table->timestamp('sent_at')->nullable();
                $table->timestamp('responded_at')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }

        // Onboarding checklists
        if (! Schema::hasTable('onboarding_templates')) {
            Schema::create('onboarding_templates', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->integer('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('onboarding_tasks')) {
            Schema::create('onboarding_tasks', function (Blueprint $table) {
                $table->id();
                $table->foreignId('candidate_id')->constrained()->cascadeOnDelete();
                $table->foreignId('template_id')->nullable()->constrained('onboarding_templates')->nullOnDelete();
                $table->string('task_name');
                $table->boolean('is_completed')->default(false);
                $table->timestamp('completed_at')->nullable();
                $table->string('document_path')->nullable();   // if task requires file upload
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }

        // Employees (post-hire)
        if (! Schema::hasTable('employees')) {
            Schema::create('employees', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->foreignId('candidate_id')->nullable()->constrained()->nullOnDelete();
                $table->string('first_name');
                $table->string('last_name');
                $table->string('email')->unique();
                $table->string('phone')->nullable();
                $table->string('role');
                $table->string('employment_type');   // Full-Time, Part-Time, 1099
                $table->string('department')->nullable();
                $table->date('start_date');
                $table->decimal('pay_rate', 10, 2)->nullable();
                $table->string('pay_type')->default('hourly');
                $table->string('location')->nullable();
                $table->boolean('is_active')->default(true);
                $table->json('access_info')->nullable();  // email, wifi, building codes
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // Employee trainings & certifications
        if (! Schema::hasTable('trainings')) {
            Schema::create('trainings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
                $table->string('name');
                $table->date('due_date')->nullable();
                $table->date('completed_date')->nullable();
                $table->boolean('is_completed')->default(false);
                $table->string('certificate_path')->nullable();
                $table->timestamps();
            });
        }

        // Time-off requests
        if (! Schema::hasTable('time_off_requests')) {
            Schema::create('time_off_requests', function (Blueprint $table) {
                $table->id();
                $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
                $table->string('type');                  // Vacation, Sick, Personal, Bereavement
                $table->date('start_date');
                $table->date('end_date');
                $table->integer('days');
                $table->text('notes')->nullable();
                $table->enum('status', ['pending', 'approved', 'denied'])->default('pending');
                $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('reviewed_at')->nullable();
                $table->timestamps();
            });
        }

        // Document uploads (shared by candidates & employees)
        if (! Schema::hasTable('documents')) {
            Schema::create('documents', function (Blueprint $table) {
                $table->id();
                $table->morphs('documentable');          // candidate or employee
                $table->string('name');
                $table->string('type')->nullable();      // resume, license, i9, w4, etc.
                $table->string('file_path');
                $table->string('mime_type')->nullable();
                $table->integer('file_size')->default(0);
                $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }

        // Status history / activity log
        if (! Schema::hasTable('activity_logs')) {
            Schema::create('activity_logs', function (Blueprint $table) {
                $table->id();
                $table->morphs('loggable');              // candidate, employee, etc.
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->string('action');                // status_change, note_added, email_sent, etc.
                $table->string('old_value')->nullable();
                $table->string('new_value')->nullable();
                $table->text('description')->nullable();
                $table->timestamps();

                $table->index(['loggable_type', 'loggable_id']);
            });
        }

        // Notifications
        if (! Schema::hasTable('notifications')) {
            Schema::create('notifications', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('type');
                $table->morphs('notifiable');
                $table->text('data');
                $table->timestamp('read_at')->nullable();
                $table->timestamps();
            });
        }

        // Automation rules config
        if (! Schema::hasTable('automation_rules')) {
            Schema::create('automation_rules', function (Blueprint $table) {
                $table->id();
                $table->string('trigger_event');         // status_changed, no_response, etc.
                $table->string('trigger_value')->nullable();
                $table->string('action_type');            // send_email, send_sms, create_task, notify
                $table->json('action_config')->nullable();
                $table->integer('delay_hours')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // Email templates
        if (! Schema::hasTable('email_templates')) {
            Schema::create('email_templates', function (Blueprint $table) {
                $table->id();
                $table->string('slug')->unique();
                $table->string('name');
                $table->string('subject');
                $table->text('body');
                $table->timestamps();
            });
        }

        // Settings
        if (! Schema::hasTable('settings')) {
            Schema::create('settings', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique();
                $table->text('value')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
        Schema::dropIfExists('email_templates');
        Schema::dropIfExists('automation_rules');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('documents');
        Schema::dropIfExists('time_off_requests');
        Schema::dropIfExists('trainings');
        Schema::dropIfExists('employees');
        Schema::dropIfExists('onboarding_tasks');
        Schema::dropIfExists('onboarding_templates');
        Schema::dropIfExists('offers');
    }
};
