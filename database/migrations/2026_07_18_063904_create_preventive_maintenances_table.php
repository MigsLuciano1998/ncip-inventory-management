<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('preventive_maintenances', function (Blueprint $table) {

            $table->id();

            $table->foreignId('equipment_id')
                ->constrained('equipment')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->date('date_inspected');

            $table->enum('status', [
                'Serviceable',
                'Unserviceable'
            ]);

            $table->text('unserviceable_reason')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Preventive Maintenance Checklist
            |--------------------------------------------------------------------------
            */

            $table->boolean('windows_update')->default(false);
            $table->text('windows_update_remarks')->nullable();

            $table->boolean('remove_unnecessary_apps')->default(false);
            $table->text('remove_unnecessary_apps_remarks')->nullable();

            $table->boolean('health_check_diagnosis')->default(false);
            $table->text('health_check_diagnosis_remarks')->nullable();

            $table->boolean('virus_scan')->default(false);
            $table->text('virus_scan_remarks')->nullable();

            $table->boolean('physical_inspection')->default(false);
            $table->text('physical_inspection_remarks')->nullable();

            $table->boolean('cdp')->default(false);
            $table->text('cdp_remarks')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Overall Remarks
            |--------------------------------------------------------------------------
            */

            $table->longText('overall_remarks')->nullable();

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('preventive_maintenances');
    }
};