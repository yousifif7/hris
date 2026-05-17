<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('payrolls')) {
            return;
        }

        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->foreignId('generated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('frequency')->default('biweekly');
            $table->date('period_start');
            $table->date('period_end');
            $table->date('pay_date')->nullable();
            $table->string('pay_type')->default('hourly');
            $table->decimal('employee_rate', 10, 2);
            $table->decimal('effective_hourly_rate', 10, 2)->nullable();
            $table->decimal('regular_hours', 8, 2)->default(0);
            $table->decimal('overtime_hours', 8, 2)->default(0);
            $table->decimal('regular_pay', 10, 2)->default(0);
            $table->decimal('overtime_pay', 10, 2)->default(0);
            $table->decimal('bonus', 10, 2)->default(0);
            $table->decimal('deductions', 10, 2)->default(0);
            $table->decimal('gross_pay', 10, 2)->default(0);
            $table->decimal('net_pay', 10, 2)->default(0);
            $table->string('status')->default('draft');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payrolls');
    }
};