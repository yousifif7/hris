<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','hr_staff','employee','candidate') NOT NULL DEFAULT 'employee'");
    }

    public function down(): void
    {
        DB::table('users')->where('role', 'candidate')->update(['role' => 'employee']);
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','hr_staff','employee') NOT NULL DEFAULT 'employee'");
    }
};
