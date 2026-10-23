<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EquipmentDepreciation extends Model
{
    protected $table = 'equipment_depreciation';

    protected $fillable = [
        'equipment_id',
        'accumulated_depreciation_beginning',
        'accumulated_depreciation_end',
        'book_value',
        'nature_of_repair',
        'repair_cost',
        'authority',
        'use_of_depreciation_date',
        'salvage_value',
        'start_date_of_depreciation',
        'status_of_depreciation',
    ];

    protected $casts = [
        'accumulated_depreciation_beginning' => 'decimal:2',
        'accumulated_depreciation_end' => 'decimal:2',
        'book_value' => 'decimal:2',
        'repair_cost' => 'decimal:2',
        'salvage_value' => 'decimal:2',
        'use_of_depreciation_date' => 'date',
        'start_date_of_depreciation' => 'date',
    ];

    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class);
    }

    public function formattedAmount(string $attribute): string
    {
        $value = $this->{$attribute};

        if ($value === null || $value === '') {
            return '—';
        }

        return number_format((float) $value, 2);
    }
}
