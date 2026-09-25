<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAircraftRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'manufacturer'               => 'required|string|max:100',
            'model'                      => 'required|string|max:100',
            'icao_type'                  => 'nullable|string|max:10',
            'iata_type'                  => 'nullable|string|max:10',
            'equivalent_aircraft'        => 'nullable|string|max:50',
            'y_seats'                    => 'nullable|integer|min:1|max:1000',
            'fuel_burn_factor'           => 'nullable|numeric|min:0',
            'passenger_to_freight_factor'=> 'nullable|numeric|between:0,1',
            'co2_factor'                 => 'nullable|numeric|min:0',
            'data_source'                => 'required|in:DEMO,ICAO,OFFICIAL,imported',
            'notes'                      => 'nullable|string',
            'status'                     => 'boolean',
        ];
    }
}
