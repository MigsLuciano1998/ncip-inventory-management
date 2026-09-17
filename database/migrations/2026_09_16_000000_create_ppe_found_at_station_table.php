<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ppe_found_at_station', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipment_id')
                ->constrained('equipment')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->string('status');
            $table->date('dates')->nullable();
            $table->timestamps();

            $table->unique('equipment_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ppe_found_at_station');
    }
};
