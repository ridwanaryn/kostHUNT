<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->owner()->create([
            'name' => 'Demo Owner',
            'email' => 'owner@ngekost.test',
        ]);

        User::factory()->tenant()->create([
            'name' => 'Demo Tenant',
            'email' => 'tenant@ngekost.test',
        ]);
    }
}
