<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('candidates', function (Blueprint $table) {
            if (! Schema::hasColumn('candidates', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('last_modified_by')
                    ->constrained('users')->nullOnDelete();
            }
        });

        // The earlier draft of this migration also added offer_letter_body — we no longer
        // want that column, so drop it if it exists from a prior run.
        if (Schema::hasColumn('candidates', 'offer_letter_body')) {
            Schema::table('candidates', function (Blueprint $table) {
                $table->dropColumn('offer_letter_body');
            });
        }
    }

    public function down(): void
    {
        Schema::table('candidates', function (Blueprint $table) {
            if (Schema::hasColumn('candidates', 'user_id')) {
                $table->dropConstrainedForeignId('user_id');
            }
        });
    }
};
