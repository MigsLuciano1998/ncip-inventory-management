<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Equipment extends Model
{
    public const CLASSIFICATION_PPE = 'ppe';

    public const CLASSIFICATION_SEMI_HV = 'semi_hv';

    public const CLASSIFICATION_LV = 'lv';

    protected $table = 'equipment';

    protected $fillable = [
        'office_id',
        'equipment_category_id',
        'equipment_type_id',
        'ppe_category_id',
        'property_no',
        'uacs_object_code',
        'tag',
        'item_id',
        'ppe_major_account_group',
        'general_ledger',
        'series_number',
        'location_number',
        'ee_seq',
        'form_series_number',
        'form_field',
        'date_purchased',
        'date_acquired',
        'cost',
        'classification',
        'fund_source',
        'estimated_useful_life',
        'par_ics',
        'par_ics_issued_date',
        'date_last_inventory',
        'status_last_inventory',
        'serial_no',
        'brand',
        'model',
        'description',
        'remarks',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
        'status_last_inventory' => 'boolean',
        'date_purchased' => 'date',
        'date_acquired' => 'date',
        'par_ics_issued_date' => 'date',
        'date_last_inventory' => 'date',
        'ppe_major_account_group' => 'integer',
        'general_ledger' => 'integer',
        'series_number' => 'integer',
        'location_number' => 'integer',
        'estimated_useful_life' => 'integer',
    ];

    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class);
    }

    public function equipmentCategory(): BelongsTo
    {
        return $this->belongsTo(EquipmentCategory::class);
    }

    public function equipmentType(): BelongsTo
    {
        return $this->belongsTo(EquipmentType::class);
    }

    public function ppeCategory(): BelongsTo
    {
        return $this->belongsTo(PpeCategory::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(EquipmentAssignment::class);
    }

    public function activeAssignment(): HasOne
    {
        return $this->hasOne(EquipmentAssignment::class)->whereNull('date_returned')->latestOfMany();
    }

    public function movementLogs(): HasMany
    {
        return $this->hasMany(EquipmentMovementLog::class)->latest();
    }

    public function preventiveMaintenances(): HasMany
    {
        return $this->hasMany(PreventiveMaintenance::class)->latest('date_inspected');
    }

    public function stationRecord(): HasOne
    {
        return $this->hasOne(PpeFoundAtStation::class);
    }

    public function lostStolenDamagedRecords(): HasMany
    {
        return $this->hasMany(LostStolenDamagedEquipment::class);
    }

    public function isFoundAtStation(): bool
    {
        return $this->stationRecord?->status === PpeFoundAtStation::STATUS_FOUND;
    }

    public function isAssigned(): bool
    {
        return $this->activeAssignment()->exists();
    }

    public static function classifications(): array
    {
        return [
            self::CLASSIFICATION_PPE => 'PPE',
            self::CLASSIFICATION_SEMI_HV => 'Semi-HV',
            self::CLASSIFICATION_LV => 'LV',
        ];
    }

    public static function parseCostAmount(mixed $cost): ?float
    {
        if ($cost === null || $cost === '') {
            return null;
        }

        $normalized = preg_replace('/[^0-9.]/', '', (string) $cost);

        if ($normalized === '' || ! is_numeric($normalized)) {
            return null;
        }

        return (float) $normalized;
    }

    public static function classifyFromCost(mixed $cost): ?string
    {
        $amount = self::parseCostAmount($cost);

        if ($amount === null) {
            return null;
        }

        if ($amount >= 50000) {
            return self::CLASSIFICATION_PPE;
        }

        if ($amount >= 5000) {
            return self::CLASSIFICATION_SEMI_HV;
        }

        if ($amount >= 1) {
            return self::CLASSIFICATION_LV;
        }

        return null;
    }

    public function classificationLabel(): string
    {
        return self::classifications()[$this->classification] ?? '—';
    }

    public function classificationBadgeClasses(): string
    {
        return match ($this->classification) {
            self::CLASSIFICATION_PPE => 'bg-indigo-100 text-indigo-800 ring-1 ring-indigo-600/20',
            self::CLASSIFICATION_SEMI_HV => 'bg-amber-100 text-amber-800 ring-1 ring-amber-600/20',
            self::CLASSIFICATION_LV => 'bg-sky-100 text-sky-800 ring-1 ring-sky-600/20',
            default => 'bg-slate-100 text-slate-600 ring-1 ring-slate-600/10',
        };
    }

    public static function buildPropertyNumber(
        mixed $tag,
        mixed $datePurchased,
        mixed $ppeMajorAccountGroup,
        mixed $generalLedger,
        mixed $seriesNumber,
        mixed $locationNumber,
    ): ?string {
        $tag = strtoupper(preg_replace('/\s+/', '', trim((string) $tag)));

        if (
            $tag === ''
            || blank($datePurchased)
            || $ppeMajorAccountGroup === null || $ppeMajorAccountGroup === ''
            || $generalLedger === null || $generalLedger === ''
            || $seriesNumber === null || $seriesNumber === ''
            || $locationNumber === null || $locationNumber === ''
        ) {
            return null;
        }

        try {
            $year = Carbon::parse($datePurchased)->format('Y');
        } catch (\Throwable) {
            return null;
        }

        return sprintf(
            '%s-%s-%02d-%02d-%04d-%02d',
            $tag,
            $year,
            (int) $ppeMajorAccountGroup,
            (int) $generalLedger,
            (int) $seriesNumber,
            (int) $locationNumber,
        );
    }
}
