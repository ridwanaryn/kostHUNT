<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

class RoomNotAvailableException extends Exception
{
    public function __construct(string $message = 'This room is not available for the selected dates.')
    {
        parent::__construct($message);
    }
}
