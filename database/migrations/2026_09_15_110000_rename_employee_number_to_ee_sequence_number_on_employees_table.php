<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('DROP INDEX IF EXISTS offices_region_id_office_name_unique');

        if (Schema::hasColumn('employees', 'employee_number') && ! Schema::hasColumn('employees', 'ee_sequence_number')) {
            DB::statement('ALTER TABLE employees RENAME COLUMN employee_number TO ee_sequence_number');
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('employees', 'ee_sequence_number') && ! Schema::hasColumn('employees', 'employee_number')) {
            DB::statement('ALTER TABLE employees RENAME COLUMN ee_sequence_number TO employee_number');
        }
    }
};
