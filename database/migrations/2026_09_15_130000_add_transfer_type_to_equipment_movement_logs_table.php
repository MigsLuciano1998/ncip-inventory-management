<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('equipment_movement_logs', function (Blueprint $table) {
            $table->string('transfer_type')->nullable()->after('action');
            $table->string('transfer_type_other')->nullable()->after('transfer_type');
        });
    }

    public function down(): void
    {
        Schema::table('equipment_movement_logs', function (Blueprint $table) {
            $table->dropColumn(['transfer_type', 'transfer_type_other']);
        });
    }
};
