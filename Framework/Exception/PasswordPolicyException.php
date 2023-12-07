<?php

namespace Framework\Exception;

use Exception;

class PasswordPolicyException extends Exception
{
    /**
     * __construct
     *
     * @return void
     */
    public function __construct(string $message = "Password does not satisfy the current policy requirements")
    {
        parent::__construct($message, 0005);
    }
    //end __construct()
}
