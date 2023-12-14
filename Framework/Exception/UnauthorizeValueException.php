<?php

declare(strict_types=1);

namespace Framework\Exception;

use Exception;

class UnauthorizeValueException extends Exception
{
    /**
     * __construct
     */
    public function __construct(string $message = 'Unauthorize values')
    {
        parent::__construct($message, 006);
    }
    // end __construct()
}
