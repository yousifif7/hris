<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('candidates', function (Blueprint $table) {
            $table->string('street_address')->nullable()->after('phone');
            $table->string('city')->nullable()->after('street_address');
            $table->string('state')->nullable()->after('city');
            $table->string('postal_code', 20)->nullable()->after('state');
            $table->string('linkedin_url')->nullable()->after('resume_file');
            $table->unsignedInteger('years_experience')->nullable()->after('linkedin_url');
            $table->boolean('is_authorized_to_work')->nullable()->after('years_experience');
            $table->decimal('desired_pay', 10, 2)->nullable()->after('is_authorized_to_work');
            $table->date('earliest_start_date')->nullable()->after('desired_pay');
        });
    }

    public function down(): void
    {
        Schema::table('candidates', function (Blueprint $table) {
            $table->dropColumn([
                'street_address',
                'city',
                'state',
                'postal_code',
                'linkedin_url',
                'years_experience',
                'is_authorized_to_work',
                'desired_pay',
                'earliest_start_date',
            ]);
        });
    }
};
