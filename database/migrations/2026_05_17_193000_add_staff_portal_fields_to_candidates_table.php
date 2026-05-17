<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('candidates', function (Blueprint $table) {
            if (! Schema::hasColumn('candidates', 'clinical_license_expires_at')) {
                $table->date('clinical_license_expires_at')->nullable()->after('availability');
            }
            if (! Schema::hasColumn('candidates', 'authorization_background_check_path')) {
                $table->string('authorization_background_check_path')->nullable()->after('clinical_license_expires_at');
            }
            if (! Schema::hasColumn('candidates', 'authorization_background_check_name')) {
                $table->string('authorization_background_check_name')->nullable()->after('authorization_background_check_path');
            }
        });
    }

    public function down(): void
    {
        Schema::table('candidates', function (Blueprint $table) {
            foreach (['clinical_license_expires_at', 'authorization_background_check_path', 'authorization_background_check_name'] as $col) {
                if (Schema::hasColumn('candidates', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
