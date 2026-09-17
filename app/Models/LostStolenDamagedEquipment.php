<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LostStolenDamagedEquipment extends Model
{
    public const STATUS_LOST = 'lost';

    public const STATUS_STOLEN = 'stolen';

    public const STATUS_DAMAGED = 'damaged';

    public const STATUS_DESTROYED = 'destroyed';

    protected $table = 'lost_stolen_damaged_equipment';

    protected $fillable = [
        'equipment_id',
        'status',
        'date',
        'remarks',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class);
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_LOST => 'Lost',
            self::STATUS_STOLEN => 'Stolen',
            self::STATUS_DAMAGED => 'Damaged',
            self::STATUS_DESTROYED => 'Destroyed',
        ];
    }

    public function statusLabel(): string
    {
        return self::statuses()[$this->status] ?? ucfirst((string) $this->status);
    }
}
