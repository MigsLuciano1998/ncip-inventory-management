<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipment_depreciation', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipment_id')
                ->unique()
                ->constrained('equipment')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->decimal('accumulated_depreciation_beginning', 15, 2)->nullable();
            $table->decimal('accumulated_depreciation_end', 15, 2)->nullable();
            $table->decimal('book_value', 15, 2)->nullable();
            $table->text('nature_of_repair')->nullable();
            $table->decimal('repair_cost', 15, 2)->nullable();
            $table->string('authority')->nullable();
            $table->date('use_of_depreciation_date')->nullable();
            $table->decimal('salvage_value', 15, 2)->nullable();
            $table->date('start_date_of_depreciation')->nullable();
            $table->string('status_of_depreciation')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipment_depreciation');
    }
};
