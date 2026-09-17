<?php

use App\Models\EquipmentCategory;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('equipment_types', function (Blueprint $table) {
            $table->foreignId('equipment_category_id')
                ->nullable()
                ->after('id')
                ->constrained()
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });

        $ictId = EquipmentCategory::query()
            ->where('short_name', 'ICT')
            ->value('id');

        if ($ictId) {
            DB::table('equipment_types')->update([
                'equipment_category_id' => $ictId,
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('equipment_types', function (Blueprint $table) {
            $table->dropConstrainedForeignId('equipment_category_id');
        });
    }
};
