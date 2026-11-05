<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Building;
use App\Models\User;

class BuildingPolicy
{
    public function view(User $user, Building $building): bool
    {
        return $building->owner_id === $user->id;
    }

    public function update(User $user, Building $building): bool
    {
        return $building->owner_id === $user->id;
    }

    public function delete(User $user, Building $building): bool
    {
        return $building->owner_id === $user->id;
    }
}
