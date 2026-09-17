<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EquipmentAssignment extends Model
{
    protected $fillable = [
        'equipment_id',
        'office_id',
        'employee_id',
        'date_assigned',
        'date_returned',
    ];

    protected $casts = [
        'date_assigned' => 'date',
        'date_returned' => 'date',
    ];

    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class);
    }

    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function movementLogs(): HasMany
    {
        return $this->hasMany(EquipmentMovementLog::class);
    }

    public function isActive(): bool
    {
        return is_null($this->date_returned);
    }

    public function scopeActive($query)
    {
        return $query->whereNull('date_returned');
    }
}
