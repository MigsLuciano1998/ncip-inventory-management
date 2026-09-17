<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    public const DEFAULT_REGION_SHORT_NAME = 'CAR';

    protected $fillable = [
        'region_short_name',
        'region_complete_name',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function provinces()
    {
        return $this->hasMany(Province::class);
    }

    public static function defaultRegionId(): int
    {
        return 2;
    }
}