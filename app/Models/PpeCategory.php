<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PpeCategory extends Model
{
    protected $fillable = [
        'number',
        'title',
        'uacs_object_code',
        'ppe_sub_major_account_group',
        'general_ledger_account',
        'status',
        'date',
    ];

    protected $casts = [
        'status' => 'boolean',
        'date' => 'date',
    ];
}
