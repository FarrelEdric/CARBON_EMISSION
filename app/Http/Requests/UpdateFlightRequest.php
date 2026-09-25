<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFlightRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'flight_number'               => 'required|string|max:20',
            'flight_date'                 => 'required|date',
            'departure_airport_id'        => 'required|exists:airports,id|different:arrival_airport_id',
            'arrival_airport_id'          => 'required|exists:airports,id',
            'aircraft_id'                 => 'required|exists:aircrafts,id',
            'total_fuel_kg'               => 'required|numeric|min:0.01',
            'passenger_to_freight_factor' => 'nullable|numeric|between:0,1',
            'y_seats'                     => 'nullable|integer|min:1',
            'passenger_load_factor'       => 'required|numeric|between:0,1',
            'co2_factor'                  => 'nullable|numeric|min:0',
            'notes'                       => 'nullable|string',
        ];
    }
}
