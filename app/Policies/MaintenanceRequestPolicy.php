<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\MaintenanceRequest;
use App\Models\User;

class MaintenanceRequestPolicy
{
    public function view(User $user, MaintenanceRequest $maintenanceRequest): bool
    {
        return $maintenanceRequest->tenant_id === $user->id
            || $maintenanceRequest->room->building->owner_id === $user->id;
    }

    public function update(User $user, MaintenanceRequest $maintenanceRequest): bool
    {
        return $maintenanceRequest->room->building->owner_id === $user->id;
    }
}
