<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ppe_categories', function (Blueprint $table) {
            $table->id();
            $table->string('number', 10)->unique();
            $table->string('title', 255);
            $table->string('uacs_object_code', 50);
            $table->string('ppe_sub_major_account_group', 10);
            $table->string('general_ledger_account', 10);
            $table->boolean('status')->default(true);
            $table->date('date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ppe_categories');
    }
};
