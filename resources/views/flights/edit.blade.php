@extends('layouts.app')

@section('title', 'Edit Penerbangan — ' . $flight->flight_number)
@section('page-title', 'Edit Data Penerbangan')
@section('page-subtitle', $flight->flight_number . ' (' . ($flight->departureAirport?->iata_code ?? '') . ' → ' . ($flight->arrivalAirport?->iata_code ?? '') . ')')

@section('content')
<div class="p-4 md:p-6 max-w-4xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-800 dark:text-slate-100">Edit Penerbangan {{ $flight->flight_number }}</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400">Perbarui parameter operasional penerbangan. Sistem akan menghitung ulang emisi karbon.</p>
        </div>
        <a href="{{ route('flights.index') }}" class="btn-secondary">
            &larr; Kembali
        </a>
    </div>

    @if($errors->any())
    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700/60 rounded-xl p-4 text-xs text-red-700 dark:text-red-300">
        <div class="font-bold mb-1">Terdapat kesalahan pengisian data:</div>
        <ul class="list-disc pl-4 space-y-0.5">
            @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="card p-6">
        <form method="POST" action="{{ route('flights.update', $flight) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- Flight Number -->
                <div>
                    <label for="flight_number" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                        Nomor Penerbangan <span class="text-rose-500">*</span>
                    </label>
                    <input type="text"
                           id="flight_number"
                           name="flight_number"
                           value="{{ old('flight_number', $flight->flight_number) }}"
                           required
                           class="w-full px-3.5 py-2.5 text-sm bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-800 dark:text-slate-100 font-mono uppercase focus:ring-2 focus:ring-[#0B5A9E] focus:outline-none">
                </div>

                <!-- Flight Date -->
                <div>
                    <label for="flight_date" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                        Tanggal Penerbangan <span class="text-rose-500">*</span>
                    </label>
                    <input type="date"
                           id="flight_date"
                           name="flight_date"
                           value="{{ old('flight_date', $flight->flight_date?->format('Y-m-d')) }}"
                           required
                           class="w-full px-3.5 py-2.5 text-sm bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-[#0B5A9E] focus:outline-none">
                </div>

                <!-- Departure Airport -->
                <div>
                    <label for="departure_airport_id" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                        Bandara Asal (Origin) <span class="text-rose-500">*</span>
                    </label>
                    <select id="departure_airport_id"
                            name="departure_airport_id"
                            required
                            class="w-full px-3.5 py-2.5 text-sm bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-[#0B5A9E] focus:outline-none">
                        @foreach($airports as $airport)
                            <option value="{{ $airport->id }}" {{ old('departure_airport_id', $flight->departure_airport_id) == $airport->id ? 'selected' : '' }}>
                                {{ $airport->iata_code }} — {{ $airport->name }} ({{ $airport->city }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Arrival Airport -->
                <div>
                    <label for="arrival_airport_id" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                        Bandara Tujuan (Destination) <span class="text-rose-500">*</span>
                    </label>
                    <select id="arrival_airport_id"
                            name="arrival_airport_id"
                            required
                            class="w-full px-3.5 py-2.5 text-sm bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-[#0B5A9E] focus:outline-none">
                        @foreach($airports as $airport)
                            <option value="{{ $airport->id }}" {{ old('arrival_airport_id', $flight->arrival_airport_id) == $airport->id ? 'selected' : '' }}>
                                {{ $airport->iata_code }} — {{ $airport->name }} ({{ $airport->city }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Aircraft -->
                <div>
                    <label for="aircraft_id" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                        Armada Pesawat <span class="text-rose-500">*</span>
                    </label>
                    <select id="aircraft_id"
                            name="aircraft_id"
                            required
                            class="w-full px-3.5 py-2.5 text-sm bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-[#0B5A9E] focus:outline-none">
                        @foreach($aircraft as $ac)
                            <option value="{{ $ac->id }}" {{ old('aircraft_id', $flight->aircraft_id) == $ac->id ? 'selected' : '' }}>
                                {{ $ac->manufacturer }} {{ $ac->model }} ({{ $ac->icao_type }}) — {{ $ac->y_seats }} Kursi
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Fuel (kg) -->
                <div>
                    <label for="total_fuel_kg" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                        Konsumsi Bahan Bakar (kg Jet-A1) <span class="text-rose-500">*</span>
                    </label>
                    <input type="number"
                           step="0.01"
                           id="total_fuel_kg"
                           name="total_fuel_kg"
                           value="{{ old('total_fuel_kg', $flight->total_fuel_kg) }}"
                           required
                           class="w-full px-3.5 py-2.5 text-sm bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-800 dark:text-slate-100 font-mono focus:ring-2 focus:ring-[#0B5A9E] focus:outline-none">
                </div>

                <!-- Passenger Load Factor -->
                <div>
                    <label for="passenger_load_factor" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                        Passenger Load Factor (0.01 – 1.00) <span class="text-rose-500">*</span>
                    </label>
                    <input type="number"
                           step="0.01"
                           min="0.01"
                           max="1"
                           id="passenger_load_factor"
                           name="passenger_load_factor"
                           value="{{ old('passenger_load_factor', $flight->passenger_load_factor) }}"
                           required
                           class="w-full px-3.5 py-2.5 text-sm bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-800 dark:text-slate-100 font-mono focus:ring-2 focus:ring-[#0B5A9E] focus:outline-none">
                </div>

                <!-- CO2 Factor -->
                <div>
                    <label for="co2_factor" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                        Faktor Emisi CO₂
                    </label>
                    <input type="number"
                           step="0.01"
                           id="co2_factor"
                           name="co2_factor"
                           value="{{ old('co2_factor', $flight->co2_factor ?? '3.16') }}"
                           class="w-full px-3.5 py-2.5 text-sm bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-800 dark:text-slate-100 font-mono focus:ring-2 focus:ring-[#0B5A9E] focus:outline-none">
                </div>
            </div>

            <!-- Notes -->
            <div>
                <label for="notes" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                    Catatan Operasional
                </label>
                <textarea id="notes"
                          name="notes"
                          rows="3"
                          class="w-full px-3.5 py-2.5 text-sm bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-[#0B5A9E] focus:outline-none">{{ old('notes', $flight->notes) }}</textarea>
            </div>

            <!-- Action buttons -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200 dark:border-slate-800">
                <a href="{{ route('flights.index') }}" class="btn-secondary">
                    Batal
                </a>
                <button type="submit" class="btn-primary">
                    Perbarui Penerbangan
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
