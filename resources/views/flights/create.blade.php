@extends('layouts.app')

@section('title', 'Add Flight')
@section('page-title', 'Add Flight Record')
@section('page-subtitle', 'Flight Operations & Emission Calculation')

@section('content')
<div class="p-4 md:p-6 max-w-4xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-800 dark:text-slate-100">Flight Entry Form</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400">Enter operational flight parameters for automated carbon footprint calculation.</p>
        </div>
        <a href="{{ route('flights.index') }}" class="btn-secondary">
            &larr; Back
        </a>
    </div>

    @if($errors->any())
    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700/60 rounded-xl p-4 text-xs text-red-700 dark:text-red-300">
        <div class="font-bold mb-1">There are errors in the submitted form:</div>
        <ul class="list-disc pl-4 space-y-0.5">
            @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="card p-6">
        <form method="POST" action="{{ route('flights.store') }}" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- Flight Number -->
                <div>
                    <label for="flight_number" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                        Flight Number <span class="text-rose-500">*</span>
                    </label>
                    <input type="text"
                           id="flight_number"
                           name="flight_number"
                           value="{{ old('flight_number') }}"
                           placeholder="e.g. GA-102 / ID-6540"
                           required
                           class="w-full px-3.5 py-2.5 text-sm bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-800 dark:text-slate-100 font-mono uppercase focus:ring-2 focus:ring-[#0B5A9E] focus:outline-none">
                </div>

                <!-- Flight Date -->
                <div>
                    <label for="flight_date" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                        Flight Date <span class="text-rose-500">*</span>
                    </label>
                    <input type="date"
                           id="flight_date"
                           name="flight_date"
                           value="{{ old('flight_date', date('Y-m-d')) }}"
                           required
                           class="w-full px-3.5 py-2.5 text-sm bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-[#0B5A9E] focus:outline-none">
                </div>

                <!-- Departure Airport -->
                <div>
                    <label for="departure_airport_id" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                        Origin Airport <span class="text-rose-500">*</span>
                    </label>
                    <select id="departure_airport_id"
                            name="departure_airport_id"
                            required
                            class="w-full px-3.5 py-2.5 text-sm bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-[#0B5A9E] focus:outline-none">
                        <option value="">Select Origin Airport</option>
                        @foreach($airports as $airport)
                            <option value="{{ $airport->id }}" {{ old('departure_airport_id') == $airport->id ? 'selected' : '' }}>
                                {{ $airport->iata_code }} — {{ $airport->name }} ({{ $airport->city }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Arrival Airport -->
                <div>
                    <label for="arrival_airport_id" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                        Destination Airport <span class="text-rose-500">*</span>
                    </label>
                    <select id="arrival_airport_id"
                            name="arrival_airport_id"
                            required
                            class="w-full px-3.5 py-2.5 text-sm bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-[#0B5A9E] focus:outline-none">
                        <option value="">Select Destination Airport</option>
                        @foreach($airports as $airport)
                            <option value="{{ $airport->id }}" {{ old('arrival_airport_id') == $airport->id ? 'selected' : '' }}>
                                {{ $airport->iata_code }} — {{ $airport->name }} ({{ $airport->city }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Aircraft -->
                <div>
                    <label for="aircraft_id" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                        Aircraft Fleet <span class="text-rose-500">*</span>
                    </label>
                    <select id="aircraft_id"
                            name="aircraft_id"
                            required
                            class="w-full px-3.5 py-2.5 text-sm bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-[#0B5A9E] focus:outline-none">
                        <option value="">Select Aircraft Type</option>
                        @foreach($aircraft as $ac)
                            <option value="{{ $ac->id }}" {{ old('aircraft_id') == $ac->id ? 'selected' : '' }}>
                                {{ $ac->manufacturer }} {{ $ac->model }} ({{ $ac->icao_type }}) — {{ $ac->y_seats }} Seats
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Fuel (kg) -->
                <div>
                    <label for="total_fuel_kg" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                        Fuel Consumption (kg Jet-A1) <span class="text-rose-500">*</span>
                    </label>
                    <input type="number"
                           step="0.01"
                           id="total_fuel_kg"
                           name="total_fuel_kg"
                           value="{{ old('total_fuel_kg') }}"
                           placeholder="e.g. 6200"
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
                           value="{{ old('passenger_load_factor', '0.82') }}"
                           placeholder="e.g. 0.82 for 82%"
                           required
                           class="w-full px-3.5 py-2.5 text-sm bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-800 dark:text-slate-100 font-mono focus:ring-2 focus:ring-[#0B5A9E] focus:outline-none">
                    <p class="mt-1 text-[11px] text-slate-400">Passenger seat occupancy ratio (ICAO standard default: 0.82).</p>
                </div>

                <!-- CO2 Factor -->
                <div>
                    <label for="co2_factor" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                        CO₂ Emission Factor (kg CO₂ / kg fuel)
                    </label>
                    <input type="number"
                           step="0.01"
                           id="co2_factor"
                           name="co2_factor"
                           value="{{ old('co2_factor', '3.16') }}"
                           class="w-full px-3.5 py-2.5 text-sm bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-800 dark:text-slate-100 font-mono focus:ring-2 focus:ring-[#0B5A9E] focus:outline-none">
                    <p class="mt-1 text-[11px] text-slate-400">Standard ICAO Jet-A1 fuel conversion factor: 3.16</p>
                </div>
            </div>

            <!-- Notes -->
            <div>
                <label for="notes" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                    Operational Notes (Optional)
                </label>
                <textarea id="notes"
                          name="notes"
                          rows="3"
                          placeholder="Additional operational remarks..."
                          class="w-full px-3.5 py-2.5 text-sm bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-[#0B5A9E] focus:outline-none">{{ old('notes') }}</textarea>
            </div>

            <!-- Action buttons -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200 dark:border-slate-800">
                <a href="{{ route('flights.index') }}" class="btn-secondary">
                    Cancel
                </a>
                <button type="submit" class="btn-primary">
                    Save Flight
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
