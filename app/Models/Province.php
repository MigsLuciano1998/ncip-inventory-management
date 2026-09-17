<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    protected $fillable = [
        'region_id',
        'province_name',
        'status'
    ];


    public function region()
    {
        return $this->belongsTo(Region::class);
    }


    public function offices()
    {
        return $this->hasMany(Office::class);
    }
}