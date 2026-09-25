<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarbonFactor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'factor_key',
        'factor_value',
        'unit',
        'description',
        'source',
        'effective_date',
        'is_active',
    ];

    protected $casts = [
        'factor_value' => 'float',
        'effective_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public static function getIcaoCo2Factor(): float
    {
        $factor = static::where('factor_key', 'icao_co2_factor')->where('is_active', true)->first();
        return $factor ? $factor->factor_value : 3.16;
    }
}
