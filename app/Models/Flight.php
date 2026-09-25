<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Flight extends Model
{
    use HasFactory;

    protected $fillable = [
        'flight_number',
        'flight_date',
        'departure_airport_id',
        'arrival_airport_id',
        'aircraft_id',
        'distance_gcd_km',
        'distance_adjusted_km',
        'total_fuel_kg',
        'passenger_to_freight_factor',
        'y_seats',
        'passenger_load_factor',
        'passenger_count',
        'co2_factor',
        'co2_total_kg',
        'co2_per_passenger_kg',
        'operational_waypoints',
        'notes',
    ];

    protected $casts = [
        'flight_date' => 'date',
        'distance_gcd_km' => 'float',
        'distance_adjusted_km' => 'float',
        'total_fuel_kg' => 'float',
        'passenger_to_freight_factor' => 'float',
        'y_seats' => 'integer',
        'passenger_load_factor' => 'float',
        'passenger_count' => 'integer',
        'co2_factor' => 'float',
        'co2_total_kg' => 'float',
        'co2_per_passenger_kg' => 'float',
        'operational_waypoints' => 'array',
    ];

    // Relationships
    public function departureAirport(): BelongsTo
    {
        return $this->belongsTo(Airport::class, 'departure_airport_id');
    }

    public function arrivalAirport(): BelongsTo
    {
        return $this->belongsTo(Airport::class, 'arrival_airport_id');
    }

    public function aircraft(): BelongsTo
    {
        return $this->belongsTo(Aircraft::class);
    }

    // Scopes
    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('flight_number', 'ilike', "%{$search}%");
        });
    }

    public function scopeByPeriod($query, ?string $period, ?string $dateFrom = null, ?string $dateTo = null)
    {
        return match($period) {
            'today' => $query->whereDate('flight_date', today()),
            'yesterday' => $query->whereDate('flight_date', today()->subDay()),
            'this-week' => $query->whereBetween('flight_date', [now()->startOfWeek(), now()->endOfWeek()]),
            'this-month' => $query->whereMonth('flight_date', now()->month)->whereYear('flight_date', now()->year),
            'this-quarter' => $query->whereBetween('flight_date', [now()->startOfQuarter(), now()->endOfQuarter()]),
            'this-year' => $query->whereYear('flight_date', now()->year),
            'custom' => $query->when($dateFrom, fn($q) => $q->whereDate('flight_date', '>=', $dateFrom))
                               ->when($dateTo, fn($q) => $q->whereDate('flight_date', '<=', $dateTo)),
            default => $query,
        };
    }

    public function scopeByDeparture($query, ?string $airportCode)
    {
        if (!$airportCode) return $query;
        return $query->whereHas('departureAirport', function ($q) use ($airportCode) {
            $q->where('iata_code', $airportCode)->orWhere('icao_code', $airportCode);
        });
    }

    public function scopeByArrival($query, ?string $airportCode)
    {
        if (!$airportCode) return $query;
        return $query->whereHas('arrivalAirport', function ($q) use ($airportCode) {
            $q->where('iata_code', $airportCode)->orWhere('icao_code', $airportCode);
        });
    }

    public function scopeByAircraft($query, ?int $aircraftId)
    {
        if (!$aircraftId) return $query;
        return $query->where('aircraft_id', $aircraftId);
    }

    public function scopeByFlightNumber($query, ?string $flightNumber)
    {
        if (!$flightNumber) return $query;
        return $query->where('flight_number', 'ilike', "%{$flightNumber}%");
    }

    // Accessors
    public function getRouteAttribute(): string
    {
        $dep = $this->departureAirport?->iata_code ?? 'N/A';
        $arr = $this->arrivalAirport?->iata_code ?? 'N/A';
        return "{$dep} → {$arr}";
    }

    public function getCo2TotalTonnesAttribute(): float
    {
        return round(($this->co2_total_kg ?? 0) / 1000, 4);
    }

    public function getLoadFactorPercentAttribute(): string
    {
        return number_format(($this->passenger_load_factor ?? 0) * 100, 1) . '%';
    }
}
