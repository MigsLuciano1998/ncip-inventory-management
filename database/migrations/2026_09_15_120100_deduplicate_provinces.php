<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $keepers = DB::table('provinces')
            ->select('region_id', 'province_name', DB::raw('MIN(id) as keep_id'))
            ->groupBy('region_id', 'province_name')
            ->get();

        foreach ($keepers as $keeper) {
            $duplicateIds = DB::table('provinces')
                ->where('region_id', $keeper->region_id)
                ->where('province_name', $keeper->province_name)
                ->where('id', '!=', $keeper->keep_id)
                ->pluck('id');

            if ($duplicateIds->isEmpty()) {
                continue;
            }

            DB::table('offices')
                ->whereIn('province_id', $duplicateIds)
                ->update(['province_id' => $keeper->keep_id]);

            DB::table('provinces')->whereIn('id', $duplicateIds)->delete();
        }

        $hasUnique = collect(Schema::getIndexes('provinces'))->contains(function (array $index) {
            return ($index['unique'] ?? false)
                && ($index['columns'] ?? []) === ['region_id', 'province_name'];
        });

        if (! $hasUnique) {
            Schema::table('provinces', function (Blueprint $table) {
                $table->unique(['region_id', 'province_name']);
            });
        }
    }

    public function down(): void
    {
        Schema::table('provinces', function (Blueprint $table) {
            $table->dropUnique(['region_id', 'province_name']);
        });
    }
};
