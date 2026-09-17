<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('equipment', function (Blueprint $table) {
            $table->string('uacs_object_code')->nullable()->after('property_no');
            $table->string('tag')->nullable()->after('uacs_object_code');
            $table->string('item_id')->nullable()->after('tag');
            $table->unsignedInteger('ppe_major_account_group')->nullable()->after('item_id');
            $table->unsignedInteger('general_ledger')->nullable()->after('ppe_major_account_group');
            $table->unsignedInteger('series_number')->nullable()->after('general_ledger');
            $table->unsignedInteger('ee_seq')->nullable()->after('series_number');
            $table->string('form_series_number')->nullable()->after('ee_seq');
            $table->string('form_field')->nullable()->after('form_series_number');
            $table->date('date_purchased')->nullable()->after('form_field');
            $table->date('date_acquired')->nullable()->after('date_purchased');
            $table->string('cost')->nullable()->after('date_acquired');
            $table->string('fund_source')->nullable()->after('cost');
            $table->unsignedTinyInteger('estimated_useful_life')->nullable()->after('fund_source');
            $table->string('par_ics')->nullable()->after('estimated_useful_life');
            $table->date('par_ics_issued_date')->nullable()->after('par_ics');
            $table->date('date_last_inventory')->nullable()->after('par_ics_issued_date');
            $table->boolean('status_last_inventory')->nullable()->after('date_last_inventory');
            $table->text('remarks')->nullable()->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('equipment', function (Blueprint $table) {
            $table->dropColumn([
                'uacs_object_code',
                'tag',
                'item_id',
                'ppe_major_account_group',
                'general_ledger',
                'series_number',
                'ee_seq',
                'form_series_number',
                'form_field',
                'date_purchased',
                'date_acquired',
                'cost',
                'fund_source',
                'estimated_useful_life',
                'par_ics',
                'par_ics_issued_date',
                'date_last_inventory',
                'status_last_inventory',
                'remarks',
            ]);
        });
    }
};
