<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PpeFoundAtStation extends Model
{
    public const STATUS_FOUND = 'found';

    public const STATUS_MISSING = 'missing';

    protected $table = 'ppe_found_at_station';

    protected $fillable = [
        'equipment_id',
        'status',
        'dates',
    ];

    protected $casts = [
        'dates' => 'date',
    ];

    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class);
    }

    public function isFound(): bool
    {
        return $this->status === self::STATUS_FOUND;
    }
}
