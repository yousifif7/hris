<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('candidates', function (Blueprint $table) {
            $table->string('schedule_token')->nullable()->unique()->after('invite_sent_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('candidates', function (Blueprint $table) {
            // SQLite refuses to drop a column while a unique index still references it,
            // so the index has to go first.
            $table->dropUnique(['schedule_token']);
            $table->dropColumn('schedule_token');
        });
    }
};
