<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Airport extends Model
{
    use HasFactory;

    protected $fillable = [
        'iata_code',
        'icao_code',
        'name',
        'city',
        'province',
        'country',
        'latitude',
        'longitude',
        'elevation',
        'status',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'elevation' => 'integer',
        'status' => 'boolean',
    ];

    // Relationships
    public function departureFlights(): HasMany
    {
        return $this->hasMany(Flight::class, 'departure_airport_id');
    }

    public function arrivalFlights(): HasMany
    {
        return $this->hasMany(Flight::class, 'arrival_airport_id');
    }

    public function departureRoutes(): HasMany
    {
        return $this->hasMany(OperationalRoute::class, 'departure_airport_id');
    }

    public function arrivalRoutes(): HasMany
    {
        return $this->hasMany(OperationalRoute::class, 'arrival_airport_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('iata_code', 'ilike', "%{$search}%")
              ->orWhere('icao_code', 'ilike', "%{$search}%")
              ->orWhere('name', 'ilike', "%{$search}%")
              ->orWhere('city', 'ilike', "%{$search}%");
        });
    }

    // Accessors
    public function getDisplayNameAttribute(): string
    {
        $code = $this->iata_code ?? $this->icao_code ?? 'N/A';
        return "{$code} — {$this->name}";
    }

    public function getCodeAttribute(): string
    {
        return $this->iata_code ?? $this->icao_code ?? 'N/A';
    }

    public function getCoordinatesAttribute(): array
    {
        return [(float) $this->latitude, (float) $this->longitude];
    }
}
