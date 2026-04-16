<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Messages table — stores both email and SMS messages
        if (! Schema::hasTable('messages')) {
            Schema::create('messages', function (Blueprint $table) {
                $table->id();
                $table->enum('type', ['email', 'sms'])->default('email');
                $table->enum('folder', ['inbox', 'sent', 'draft', 'trash'])->default('draft');
                $table->foreignId('candidate_id')->nullable()->constrained()->nullOnDelete();
                $table->foreignId('template_id')->nullable()->constrained('email_templates')->nullOnDelete();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->string('from')->nullable();
                $table->string('to')->nullable();
                $table->string('cc')->nullable();
                $table->string('bcc')->nullable();
                $table->string('subject')->nullable();
                $table->text('body');
                $table->boolean('is_read')->default(false);
                $table->boolean('is_html')->default(false);
                $table->timestamp('sent_at')->nullable();
                $table->timestamps();

                $table->index(['type', 'folder']);
                $table->index('candidate_id');
            });
        }

        // Add offer token + viewed_at to offers
        if (Schema::hasTable('offers')) {
            if (! Schema::hasColumn('offers', 'token')) {
                Schema::table('offers', function (Blueprint $table) {
                    $table->string('token')->nullable()->unique()->after('id');
                    $table->timestamp('viewed_at')->nullable()->after('responded_at');
                    $table->text('notes')->nullable()->after('viewed_at');
                });
            }
        }

        // Add HTML body to email_templates
        if (Schema::hasTable('email_templates')) {
            if (! Schema::hasColumn('email_templates', 'body_html')) {
                Schema::table('email_templates', function (Blueprint $table) {
                    $table->text('body_html')->nullable()->after('body');
                    $table->string('category')->nullable()->after('slug'); // e.g. candidate, offer, onboarding
                    $table->boolean('is_active')->default(true)->after('category');
                });
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');

        if (Schema::hasTable('offers')) {
            Schema::table('offers', function (Blueprint $table) {
                $table->dropColumn(['token', 'viewed_at', 'notes']);
            });
        }

        if (Schema::hasTable('email_templates')) {
            Schema::table('email_templates', function (Blueprint $table) {
                $table->dropColumn(['body_html', 'category', 'is_active']);
            });
        }
    }
};
