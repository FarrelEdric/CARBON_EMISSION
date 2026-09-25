<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FlightController;
use App\Http\Controllers\AirportController;
use App\Http\Controllers\AircraftController;
use App\Http\Controllers\CarbonCalculatorController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\OperationalRouteController;
use App\Http\Controllers\CarbonFactorController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Support\Facades\Route;

// Redirect root to login or dashboard
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// =============================================================
// AUTHENTICATED ROUTES
// =============================================================
Route::middleware(['auth', 'active'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/map-data', [DashboardController::class, 'mapData'])->name('dashboard.map-data');
    Route::get('/dashboard/chart-data', [DashboardController::class, 'chartData'])->name('dashboard.chart-data');
    Route::get('/dashboard/live-flights', [DashboardController::class, 'liveFlights'])->name('dashboard.live-flights');

    // Carbon Calculator
    Route::get('/carbon-calculator', [CarbonCalculatorController::class, 'index'])->name('carbon-calculator.index');
    Route::post('/carbon-calculator/calculate', [CarbonCalculatorController::class, 'calculate'])->name('carbon-calculator.calculate');

    // Flight Monitoring
    Route::get('/flights', [FlightController::class, 'index'])->name('flights.index');
    Route::get('/flights/create', [FlightController::class, 'create'])->name('flights.create');
    Route::post('/flights', [FlightController::class, 'store'])->name('flights.store');
    Route::get('/flights/{flight}', [FlightController::class, 'show'])->name('flights.show');
    Route::get('/flights/{flight}/edit', [FlightController::class, 'edit'])->name('flights.edit');
    Route::put('/flights/{flight}', [FlightController::class, 'update'])->name('flights.update');
    Route::delete('/flights/{flight}', [FlightController::class, 'destroy'])->name('flights.destroy');
    Route::get('/flights/export/excel', [FlightController::class, 'exportExcel'])->name('flights.export-excel');
    Route::post('/flights/import/excel', [FlightController::class, 'importExcel'])->name('flights.import-excel');

    // Master Data - Airports
    Route::get('/airports', [AirportController::class, 'index'])->name('airports.index');
    Route::get('/airports/create', [AirportController::class, 'create'])->name('airports.create');
    Route::post('/airports', [AirportController::class, 'store'])->name('airports.store');
    Route::get('/airports/{airport}', [AirportController::class, 'show'])->name('airports.show');
    Route::get('/airports/{airport}/edit', [AirportController::class, 'edit'])->name('airports.edit');
    Route::put('/airports/{airport}', [AirportController::class, 'update'])->name('airports.update');
    Route::delete('/airports/{airport}', [AirportController::class, 'destroy'])->name('airports.destroy');
    Route::get('/airports/export/excel', [AirportController::class, 'exportExcel'])->name('airports.export-excel');
    Route::post('/airports/import/excel', [AirportController::class, 'importExcel'])->name('airports.import-excel');
    Route::get('/airports/search/json', [AirportController::class, 'search'])->name('airports.search');

    // Master Data - Aircraft
    Route::get('/aircraft', [AircraftController::class, 'index'])->name('aircraft.index');
    Route::get('/aircraft/create', [AircraftController::class, 'create'])->name('aircraft.create');
    Route::post('/aircraft', [AircraftController::class, 'store'])->name('aircraft.store');
    Route::get('/aircraft/{aircraft}', [AircraftController::class, 'show'])->name('aircraft.show');
    Route::get('/aircraft/{aircraft}/edit', [AircraftController::class, 'edit'])->name('aircraft.edit');
    Route::put('/aircraft/{aircraft}', [AircraftController::class, 'update'])->name('aircraft.update');
    Route::delete('/aircraft/{aircraft}', [AircraftController::class, 'destroy'])->name('aircraft.destroy');
    Route::get('/aircraft/export/excel', [AircraftController::class, 'exportExcel'])->name('aircraft.export-excel');
    Route::post('/aircraft/import/excel', [AircraftController::class, 'importExcel'])->name('aircraft.import-excel');

    // Master Data - Operational Routes
    Route::get('/operational-routes/export/excel', [OperationalRouteController::class, 'exportExcel'])->name('operational-routes.export-excel');
    Route::resource('operational-routes', OperationalRouteController::class);

    // Master Data - Carbon Factors
    Route::get('/carbon-factors/export/excel', [CarbonFactorController::class, 'exportExcel'])->name('carbon-factors.export-excel');
    Route::resource('carbon-factors', CarbonFactorController::class);

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export/excel', [ReportController::class, 'exportExcel'])->name('reports.export-excel');

    // Admin
    Route::prefix('admin')->name('admin.')->group(function () {
        // Users
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::put('/users/{user}/password', [UserController::class, 'updatePassword'])->name('users.update-password');

        // Settings
        Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::put('/settings/password', [SettingsController::class, 'updatePassword'])->name('settings.update-password');
        Route::put('/settings/profile', [SettingsController::class, 'updateProfile'])->name('settings.update-profile');
        Route::put('/settings/theme', [SettingsController::class, 'updateTheme'])->name('settings.update-theme');
    });
});

// Authentication routes (Breeze)
require __DIR__.'/auth.php';
