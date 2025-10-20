<?php

namespace App\Exception;

use Exception;

class BookingConflictException extends Exception
{
    public function __construct(string $message)
    {
        parent::__construct($message, 409);
    }
}
