<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Room;
use App\Models\User;

class RoomPolicy
{
    public function view(User $user, Room $room): bool
    {
        return $this->ownsRoom($user, $room);
    }

    public function update(User $user, Room $room): bool
    {
        return $this->ownsRoom($user, $room);
    }

    public function delete(User $user, Room $room): bool
    {
        return $this->ownsRoom($user, $room);
    }

    private function ownsRoom(User $user, Room $room): bool
    {
        return $room->building->owner_id === $user->id;
    }
}
