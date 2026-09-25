<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create default admin user
        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'name'     => 'Administrator',
                'username' => 'admin',
                'email'    => 'admin@ace.airnav.local',
                'password' => Hash::make('admin'),
                'role'     => 'admin',
                'status'   => true,
            ]
        );

        $this->command->info(' Admin user created: username=admin / password=admin');
        $this->command->warn(' Ganti password admin sebelum production!');

        // Run all seeders in order
        $this->call([
            CarbonFactorSeeder::class,
            AirportSeeder::class,
            AircraftSeeder::class,
            FlightSeeder::class,
            OperationalRouteSeeder::class,
        ]);
    }
}
