<?php

declare(strict_types=1);

namespace Framework\Exception;

use Exception;

class DisableUserException extends Exception
{
    /**
     * __construct
     */
    public function __construct(string $message = 'disable user')
    {
        parent::__construct($message, 0010);
    }
    // end __construct()
}
