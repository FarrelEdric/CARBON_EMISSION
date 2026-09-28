<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    private function createUser(string $role): User
    {
        return User::create([
            'name'     => ucfirst($role) . ' User',
            'username' => $role . '_user',
            'email'    => $role . '@ace.local',
            'password' => bcrypt('password'),
            'role'     => $role,
            'status'   => true,
        ]);
    }

    public function test_admin_has_full_access()
    {
        $admin = $this->createUser('admin');

        $this->actingAs($admin)->get('/dashboard')->assertOk();
        $this->actingAs($admin)->get('/admin/users')->assertOk();
        $this->actingAs($admin)->get('/flights/create')->assertOk();
        $this->actingAs($admin)->get('/carbon-calculator')->assertOk();
        $this->actingAs($admin)->get('/airports/create')->assertOk();
        $this->actingAs($admin)->get('/aircraft/create')->assertOk();
        $this->actingAs($admin)->get('/operational-routes/create')->assertOk();
        $this->actingAs($admin)->get('/carbon-factors/create')->assertOk();
        $this->actingAs($admin)->get('/reports')->assertOk();
    }

    public function test_operator_access()
    {
        $operator = $this->createUser('operator');

        // Operator can manage operational data & calculator
        $this->actingAs($operator)->get('/dashboard')->assertOk();
        $this->actingAs($operator)->get('/flights/create')->assertOk();
        $this->actingAs($operator)->get('/carbon-calculator')->assertOk();
        $this->actingAs($operator)->get('/airports/create')->assertOk();
        $this->actingAs($operator)->get('/aircraft/create')->assertOk();
        $this->actingAs($operator)->get('/operational-routes/create')->assertOk();
        $this->actingAs($operator)->get('/carbon-factors/create')->assertOk();
        $this->actingAs($operator)->get('/reports')->assertOk();

        // Operator CANNOT access admin area
        $this->actingAs($operator)->get('/admin/users')->assertStatus(403);
        $this->actingAs($operator)->get('/admin/settings')->assertStatus(403);
    }

    public function test_viewer_access()
    {
        $viewer = $this->createUser('viewer');

        // Viewer can ONLY view Dashboard, Flights, and Reports
        $this->actingAs($viewer)->get('/dashboard')->assertOk();
        $this->actingAs($viewer)->get('/flights')->assertOk();
        $this->actingAs($viewer)->get('/reports')->assertOk();

        // Viewer CANNOT access Master Data
        $this->actingAs($viewer)->get('/airports')->assertStatus(403);
        $this->actingAs($viewer)->get('/aircraft')->assertStatus(403);
        $this->actingAs($viewer)->get('/operational-routes')->assertStatus(403);
        $this->actingAs($viewer)->get('/carbon-factors')->assertStatus(403);
        $this->actingAs($viewer)->get('/airports/create')->assertStatus(403);
        $this->actingAs($viewer)->get('/aircraft/create')->assertStatus(403);
        $this->actingAs($viewer)->get('/operational-routes/create')->assertStatus(403);
        $this->actingAs($viewer)->get('/carbon-factors/create')->assertStatus(403);

        // Viewer CANNOT access Calculator or Admin area
        $this->actingAs($viewer)->get('/flights/create')->assertStatus(403);
        $this->actingAs($viewer)->post('/flights', [])->assertStatus(403);
        $this->actingAs($viewer)->get('/carbon-calculator')->assertStatus(403);
        $this->actingAs($viewer)->get('/admin/users')->assertStatus(403);
        $this->actingAs($viewer)->get('/admin/settings')->assertStatus(403);
    }
}
