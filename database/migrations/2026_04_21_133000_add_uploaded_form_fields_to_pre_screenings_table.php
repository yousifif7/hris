<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pre_screenings', function (Blueprint $table) {
            $table->string('uploaded_form_path')->nullable()->after('additional_notes');
            $table->string('uploaded_form_name')->nullable()->after('uploaded_form_path');
        });
    }

    public function down(): void
    {
        Schema::table('pre_screenings', function (Blueprint $table) {
            $table->dropColumn(['uploaded_form_path', 'uploaded_form_name']);
        });
    }
};
