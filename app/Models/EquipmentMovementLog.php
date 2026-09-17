<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EquipmentMovementLog extends Model
{
    public const ACTION_CREATED = 'created';

    public const ACTION_UPDATED = 'updated';

    public const ACTION_ASSIGNED = 'assigned';

    public const ACTION_RETURNED = 'returned';

    public const ACTION_TRANSFERRED = 'transferred';

    public const ACTION_DELETED = 'deleted';

    public const TRANSFER_DONATION = 'donation';

    public const TRANSFER_RELOCATE = 'relocate';

    public const TRANSFER_REASSIGNMENT = 'reassignment';

    public const TRANSFER_OTHERS = 'others';

    protected $fillable = [
        'equipment_id',
        'equipment_assignment_id',
        'action',
        'transfer_type',
        'transfer_type_other',
        'owner_name',
        'previous_owner_name',
        'office_id',
        'previous_office_id',
        'remarks',
    ];

    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class);
    }

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(EquipmentAssignment::class, 'equipment_assignment_id');
    }

    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class);
    }

    public function previousOffice(): BelongsTo
    {
        return $this->belongsTo(Office::class, 'previous_office_id');
    }

    public static function record(array $attributes): self
    {
        return static::create($attributes);
    }

    public function actionLabel(): string
    {
        return match ($this->action) {
            self::ACTION_CREATED => 'Registered',
            self::ACTION_UPDATED => 'Updated',
            self::ACTION_ASSIGNED => 'Assigned',
            self::ACTION_RETURNED => 'Returned',
            self::ACTION_TRANSFERRED => 'Transferred',
            self::ACTION_DELETED => 'Deleted',
            default => ucfirst($this->action),
        };
    }

    public function employeeDisplayName(): string
    {
        if ($this->action === self::ACTION_TRANSFERRED && $this->previous_owner_name) {
            return $this->previous_owner_name.' → '.($this->owner_name ?: '—');
        }

        return $this->owner_name
            ?? $this->assignment?->employee?->full_name
            ?? '—';
    }
}
