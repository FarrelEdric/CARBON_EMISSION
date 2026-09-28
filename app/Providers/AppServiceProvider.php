<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Admin: Full access including user management & system settings
        Gate::define('admin-only', fn(User $user) => $user->isAdmin());
        Gate::define('manage-users', fn(User $user) => $user->isAdmin());
        Gate::define('manage-settings', fn(User $user) => $user->isAdmin());

        // Operator & Admin: Manage flights, master data (airports, aircraft, routes, factors), and calculator
        Gate::define('manage-data', fn(User $user) => $user->canManageData());
        Gate::define('manage-flights', fn(User $user) => $user->canManageData());
        Gate::define('manage-airports', fn(User $user) => $user->canManageData());
        Gate::define('manage-aircraft', fn(User $user) => $user->canManageData());
        Gate::define('manage-routes', fn(User $user) => $user->canManageData());
        Gate::define('manage-factors', fn(User $user) => $user->canManageData());
        Gate::define('use-calculator', fn(User $user) => $user->canManageData());
    }
}
