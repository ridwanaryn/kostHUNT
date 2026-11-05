<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Building;
use App\Models\Photo;
use App\Models\Room;
use App\Models\User;

class PhotoPolicy
{
    public function update(User $user, Photo $photo): bool
    {
        return $this->ownsPhoto($user, $photo);
    }

    public function delete(User $user, Photo $photo): bool
    {
        return $this->ownsPhoto($user, $photo);
    }

    private function ownsPhoto(User $user, Photo $photo): bool
    {
        $imageable = $photo->imageable;

        return match (true) {
            $imageable instanceof Building => $imageable->owner_id === $user->id,
            $imageable instanceof Room => $imageable->building->owner_id === $user->id,
            default => false,
        };
    }
}
