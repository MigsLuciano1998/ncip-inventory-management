<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipment_movement_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipment_id')->constrained('equipment')->cascadeOnDelete();
            $table->foreignId('equipment_assignment_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action');
            $table->string('owner_name')->nullable();
            $table->string('previous_owner_name')->nullable();
            $table->foreignId('office_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('previous_office_id')->nullable()->constrained('offices')->nullOnDelete();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipment_movement_logs');
    }
};
