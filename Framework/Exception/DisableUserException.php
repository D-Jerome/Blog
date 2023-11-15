<?php

namespace Framework\Exception;

use Exception;

class DisableUserException extends Exception
{
    public function __construct($message = "disable user")
    {
        parent::__construct($message, "0010");
    }
}
