<?php

namespace Framework\Exception;

use Exception;

class PasswordPolicyException extends Exception
{
    public function __construct($message = "Password does not satisfy the current policy requirements")
    {
        parent::__construct($message, "0008");
    }
}
