<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Use TEXT to keep these off-row — the candidates table is close to the row-size limit.
        Schema::table('candidates', function (Blueprint $table) {
            if (! Schema::hasColumn('candidates', 'background_check_path')) {
                $table->text('background_check_path')->nullable();
            }
            if (! Schema::hasColumn('candidates', 'background_check_name')) {
                $table->text('background_check_name')->nullable();
            }
            if (! Schema::hasColumn('candidates', 'team')) {
                $table->text('team')->nullable();
            }
        });

        if (! Schema::hasTable('candidate_collaborators')) {
            Schema::create('candidate_collaborators', function (Blueprint $table) {
                $table->id();
                $table->foreignId('candidate_id')->constrained()->cascadeOnDelete();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->timestamps();
                $table->unique(['candidate_id', 'user_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('candidate_collaborators');

        Schema::table('candidates', function (Blueprint $table) {
            foreach (['background_check_path', 'background_check_name', 'team'] as $col) {
                if (Schema::hasColumn('candidates', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
