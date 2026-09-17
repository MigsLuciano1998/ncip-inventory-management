<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PreventiveMaintenance extends Model
{
    public const STATUS_SERVICEABLE = 'Serviceable';

    public const STATUS_UNSERVICEABLE = 'Unserviceable';

    protected $fillable = [
        'equipment_id',
        'date_inspected',
        'status',
        'unserviceable_reason',
        'windows_update',
        'windows_update_remarks',
        'remove_unnecessary_apps',
        'remove_unnecessary_apps_remarks',
        'health_check_diagnosis',
        'health_check_diagnosis_remarks',
        'virus_scan',
        'virus_scan_remarks',
        'physical_inspection',
        'physical_inspection_remarks',
        'cdp',
        'cdp_remarks',
        'overall_remarks',
    ];

    protected $casts = [
        'date_inspected' => 'date',
        'windows_update' => 'boolean',
        'remove_unnecessary_apps' => 'boolean',
        'health_check_diagnosis' => 'boolean',
        'virus_scan' => 'boolean',
        'physical_inspection' => 'boolean',
        'cdp' => 'boolean',
    ];

    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class);
    }

    public function checklistCompletedCount(): int
    {
        return collect([
            $this->windows_update,
            $this->remove_unnecessary_apps,
            $this->health_check_diagnosis,
            $this->virus_scan,
            $this->physical_inspection,
            $this->cdp,
        ])->filter()->count();
    }

    public function isServiceable(): bool
    {
        return $this->status === self::STATUS_SERVICEABLE;
    }
}
