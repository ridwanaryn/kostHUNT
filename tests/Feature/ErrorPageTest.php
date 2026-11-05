<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ErrorPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_not_found_page_is_custom(): void
    {
        $this->get('/this-page-does-not-exist')
            ->assertNotFound()
            ->assertSee('Page not found');
    }

    public function test_forbidden_page_is_custom(): void
    {
        $tenant = User::factory()->tenant()->create();

        $this->actingAs($tenant)
            ->get(route('owner.buildings.index'))
            ->assertForbidden()
            ->assertSee('Access denied');
    }
}
