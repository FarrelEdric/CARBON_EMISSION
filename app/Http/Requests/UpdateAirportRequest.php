<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAirportRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $id = $this->route('airport')->id ?? null;
        return [
            'iata_code'  => "nullable|string|max:10|unique:airports,iata_code,{$id}",
            'icao_code'  => "nullable|string|max:10|unique:airports,icao_code,{$id}",
            'name'       => 'required|string|max:200',
            'city'       => 'nullable|string|max:100',
            'province'   => 'nullable|string|max:100',
            'country'    => 'nullable|string|max:10',
            'latitude'   => 'nullable|numeric|between:-90,90',
            'longitude'  => 'nullable|numeric|between:-180,180',
            'elevation'  => 'nullable|integer',
            'status'     => 'boolean',
        ];
    }
}
