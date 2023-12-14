<?php

declare(strict_types=1);

namespace Framework\Exception;

use Exception;

class PasswordPolicyException extends Exception
{
    /**
     * __construct
     */
    public function __construct(string $message = 'Password does not satisfy the current policy requirements')
    {
        parent::__construct($message, 0005);
    }
    // end __construct()
}
