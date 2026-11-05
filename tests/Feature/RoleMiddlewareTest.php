<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class RoleMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Route::middleware(['web', 'auth', 'role:owner'])
            ->get('/_test/owner-only', fn (): string => 'owner area');

        Route::middleware(['web', 'auth', 'role:tenant'])
            ->get('/_test/tenant-only', fn (): string => 'tenant area');
    }

    public function test_owner_can_access_owner_route(): void
    {
        $owner = User::factory()->owner()->create();

        $this->actingAs($owner)
            ->get('/_test/owner-only')
            ->assertOk()
            ->assertSee('owner area');
    }

    public function test_tenant_cannot_access_owner_route(): void
    {
        $tenant = User::factory()->tenant()->create();

        $this->actingAs($tenant)
            ->get('/_test/owner-only')
            ->assertForbidden();
    }

    public function test_owner_cannot_access_tenant_route(): void
    {
        $owner = User::factory()->owner()->create();

        $this->actingAs($owner)
            ->get('/_test/tenant-only')
            ->assertForbidden();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/_test/owner-only')->assertRedirect(route('login'));
    }
}
