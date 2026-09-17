<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('equipment', function (Blueprint $table) {
            $table->foreignId('ppe_category_id')
                ->nullable()
                ->after('equipment_type_id')
                ->constrained('ppe_categories')
                ->nullOnDelete();
        });

        Schema::table('equipment', function (Blueprint $table) {
            $table->string('ee_seq_tmp')->nullable();
        });

        DB::table('equipment')->update([
            'ee_seq_tmp' => DB::raw('CAST(ee_seq AS TEXT)'),
        ]);

        Schema::table('equipment', function (Blueprint $table) {
            $table->dropColumn('ee_seq');
        });

        Schema::table('equipment', function (Blueprint $table) {
            $table->string('ee_seq')->nullable()->after('location_number');
        });

        DB::table('equipment')->update([
            'ee_seq' => DB::raw('ee_seq_tmp'),
        ]);

        Schema::table('equipment', function (Blueprint $table) {
            $table->dropColumn('ee_seq_tmp');
        });
    }

    public function down(): void
    {
        Schema::table('equipment', function (Blueprint $table) {
            $table->unsignedInteger('ee_seq_tmp')->nullable();
        });

        DB::table('equipment')->update([
            'ee_seq_tmp' => DB::raw("CAST(ee_seq AS INTEGER)"),
        ]);

        Schema::table('equipment', function (Blueprint $table) {
            $table->dropColumn('ee_seq');
        });

        Schema::table('equipment', function (Blueprint $table) {
            $table->unsignedInteger('ee_seq')->nullable()->after('location_number');
        });

        DB::table('equipment')->update([
            'ee_seq' => DB::raw('ee_seq_tmp'),
        ]);

        Schema::table('equipment', function (Blueprint $table) {
            $table->dropColumn('ee_seq_tmp');
            $table->dropConstrainedForeignId('ppe_category_id');
        });
    }
};
