<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The Pre-Onboard tab now stores uploaded files in the existing string columns
     * (path is stored there). Each gets a TEXT companion column for the original
     * filename so we can render upload chips. TEXT keeps the data off-row and avoids
     * worsening the candidates row-size limit.
     */
    public function up(): void
    {
        $fields = [
            'college_degree',
            'college_transcripts',
            'cpr_certification',
            'child_registry_clearance',
            'tb_test_results',
            'dwihn_transcripts',
            'i9_document',
        ];

        Schema::table('candidates', function (Blueprint $table) use ($fields) {
            foreach ($fields as $field) {
                $nameCol = $field.'_name';
                if (! Schema::hasColumn('candidates', $nameCol)) {
                    $table->text($nameCol)->nullable();
                }
            }
        });
    }

    public function down(): void
    {
        $fields = [
            'college_degree',
            'college_transcripts',
            'cpr_certification',
            'child_registry_clearance',
            'tb_test_results',
            'dwihn_transcripts',
            'i9_document',
        ];

        Schema::table('candidates', function (Blueprint $table) use ($fields) {
            foreach ($fields as $field) {
                $nameCol = $field.'_name';
                if (Schema::hasColumn('candidates', $nameCol)) {
                    $table->dropColumn($nameCol);
                }
            }
        });
    }
};
