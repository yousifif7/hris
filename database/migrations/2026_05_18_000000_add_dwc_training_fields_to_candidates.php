<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adds the DWC Trainings tab columns: one master "DWC Transcript" plus a
     * status + expiration date pair for each of the 13 DWC required trainings.
     */
    public function up(): void
    {
        // Status columns use TEXT instead of VARCHAR — the candidates table is already
        // at MySQL's 65,535-byte inline row-size limit (too many varchar(255) columns).
        // TEXT is stored off-row in InnoDB and doesn't count against the limit.
        Schema::table('candidates', function (Blueprint $table) {
            if (! Schema::hasColumn('candidates', 'dwc_transcript')) {
                $table->text('dwc_transcript')->nullable();
            }

            foreach ($this->trainingKeys() as $key) {
                $statusCol  = "dwc_{$key}_status";
                $expiresCol = "dwc_{$key}_expires_at";
                if (! Schema::hasColumn('candidates', $statusCol)) {
                    $table->text($statusCol)->nullable();
                }
                if (! Schema::hasColumn('candidates', $expiresCol)) {
                    $table->date($expiresCol)->nullable();
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('candidates', function (Blueprint $table) {
            if (Schema::hasColumn('candidates', 'dwc_transcript')) {
                $table->dropColumn('dwc_transcript');
            }
            foreach ($this->trainingKeys() as $key) {
                foreach (["dwc_{$key}_status", "dwc_{$key}_expires_at"] as $col) {
                    if (Schema::hasColumn('candidates', $col)) {
                        $table->dropColumn($col);
                    }
                }
            }
        });
    }

    private function trainingKeys(): array
    {
        return [
            'abuse_neglect',
            'anti_harassment',
            'cultural_competence',
            'emergency_preparedness',
            'grievances',
            'hipaa_basics',
            'sex_trafficking',
            'infection_prevention',
            'lep',
            'medicare_compliance',
            'medicare_fraud',
            'person_centered',
            'recipient_rights',
        ];
    }
};
