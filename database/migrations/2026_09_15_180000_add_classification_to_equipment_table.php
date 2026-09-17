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
            $table->string('classification')->nullable()->after('cost');
        });

        foreach (DB::table('equipment')->orderBy('id')->get() as $row) {
            $classification = $this->classifyFromCost($row->cost);

            if ($classification) {
                DB::table('equipment')->where('id', $row->id)->update([
                    'classification' => $classification,
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('equipment', function (Blueprint $table) {
            $table->dropColumn('classification');
        });
    }

    private function classifyFromCost(mixed $cost): ?string
    {
        if ($cost === null || $cost === '') {
            return null;
        }

        $normalized = preg_replace('/[^0-9.]/', '', (string) $cost);

        if ($normalized === '' || ! is_numeric($normalized)) {
            return null;
        }

        $amount = (float) $normalized;

        if ($amount >= 50000) {
            return 'ppe';
        }

        if ($amount >= 5000) {
            return 'semi_hv';
        }

        if ($amount >= 1) {
            return 'lv';
        }

        return null;
    }
};
