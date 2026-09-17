<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Employee extends Model
{
    protected $fillable = [
        'ee_sequence_number',
        'first_name',
        'middle_name',
        'last_name',
        'office_id',
        'position',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class);
    }

    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . ($this->middle_name ? $this->middle_name . ' ' : '') . $this->last_name);
    }

    public function getLastNameFirstAttribute(): string
    {
        $given = trim($this->first_name . ' ' . ($this->middle_name ?? ''));

        return trim($this->last_name . ($given !== '' ? ', ' . $given : ''));
    }
}
