<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OperationalRoute extends Model
{
    use HasFactory;

    protected $fillable = [
        'departure_airport_id',
        'arrival_airport_id',
        'route_name',
        'waypoints',
        'distance_km',
        'source',
        'status',
    ];

    protected $casts = [
        'waypoints' => 'array',
        'distance_km' => 'float',
        'status' => 'boolean',
    ];

    public function departureAirport(): BelongsTo
    {
        return $this->belongsTo(Airport::class, 'departure_airport_id');
    }

    public function arrivalAirport(): BelongsTo
    {
        return $this->belongsTo(Airport::class, 'arrival_airport_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function isDemo(): bool
    {
        return $this->source === 'demo';
    }

    public function getSourceLabelAttribute(): string
    {
        return match($this->source) {
            'demo' => 'Demo Operational Route',
            'official' => 'Rute Resmi',
            'imported' => 'Data Import',
            default => ucfirst($this->source),
        };
    }
}
