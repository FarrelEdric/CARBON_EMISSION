<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Aircraft extends Model
{
    use HasFactory;

    protected $table = 'aircrafts';

    protected $fillable = [
        'manufacturer',
        'model',
        'icao_type',
        'iata_type',
        'equivalent_aircraft',
        'y_seats',
        'fuel_burn_factor',
        'passenger_to_freight_factor',
        'co2_factor',
        'data_source',
        'notes',
        'status',
    ];

    protected $casts = [
        'y_seats' => 'integer',
        'fuel_burn_factor' => 'float',
        'passenger_to_freight_factor' => 'float',
        'co2_factor' => 'float',
        'status' => 'boolean',
    ];

    // Relationships
    public function flights(): HasMany
    {
        return $this->hasMany(Flight::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('manufacturer', 'ilike', "%{$search}%")
              ->orWhere('model', 'ilike', "%{$search}%")
              ->orWhere('icao_type', 'ilike', "%{$search}%")
              ->orWhere('iata_type', 'ilike', "%{$search}%");
        });
    }

    // Accessors
    public function getFullNameAttribute(): string
    {
        return "{$this->manufacturer} {$this->model}";
    }

    public function getDisplayNameAttribute(): string
    {
        $type = $this->icao_type ?? $this->iata_type ?? '';
        return $type ? "{$this->manufacturer} {$this->model} ({$type})" : "{$this->manufacturer} {$this->model}";
    }

    public function isDemo(): bool
    {
        return $this->data_source === 'DEMO';
    }
}
