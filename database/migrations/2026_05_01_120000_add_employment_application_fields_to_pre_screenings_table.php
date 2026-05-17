<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pre_screenings', function (Blueprint $table) {
            $table->json('employment_application_data')->nullable()->after('uploaded_form_name');
            $table->timestamp('employment_application_submitted_at')->nullable()->after('employment_application_data');
        });
    }

    public function down(): void
    {
        Schema::table('pre_screenings', function (Blueprint $table) {
            $table->dropColumn(['employment_application_data', 'employment_application_submitted_at']);
        });
    }
};
