<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('equipment', function (Blueprint $table) {
            $table->dropForeign(['equipment_type_id']);
        });

        Schema::table('equipment', function (Blueprint $table) {
            $table->unsignedBigInteger('equipment_type_id')->nullable()->change();
        });

        Schema::table('equipment', function (Blueprint $table) {
            $table->foreign('equipment_type_id')
                ->references('id')
                ->on('equipment_types')
                ->cascadeOnUpdate()
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('equipment', function (Blueprint $table) {
            $table->dropForeign(['equipment_type_id']);
        });

        Schema::table('equipment', function (Blueprint $table) {
            $table->unsignedBigInteger('equipment_type_id')->nullable(false)->change();
        });

        Schema::table('equipment', function (Blueprint $table) {
            $table->foreign('equipment_type_id')
                ->references('id')
                ->on('equipment_types')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });
    }
};
