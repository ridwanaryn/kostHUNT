<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FlashToastTest extends TestCase
{
    use RefreshDatabase;

    public function test_flash_status_renders_in_the_app_shell(): void
    {
        $tenant = User::factory()->tenant()->create();

        $this->actingAs($tenant)
            ->withSession(['status' => 'Toast wiring works.'])
            ->get(route('tenant.dashboard'))
            ->assertOk()
            ->assertSee('Toast wiring works.');
    }
}
