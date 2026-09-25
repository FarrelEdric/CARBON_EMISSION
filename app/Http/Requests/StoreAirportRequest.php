<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAirportRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'iata_code'  => 'nullable|string|max:10|unique:airports,iata_code',
            'icao_code'  => 'nullable|string|max:10|unique:airports,icao_code',
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

    public function messages(): array
    {
        return [
            'name.required'      => 'Nama bandara wajib diisi.',
            'iata_code.unique'   => 'Kode IATA sudah digunakan.',
            'icao_code.unique'   => 'Kode ICAO sudah digunakan.',
            'latitude.between'   => 'Latitude harus antara -90 dan 90.',
            'longitude.between'  => 'Longitude harus antara -180 dan 180.',
        ];
    }
}
