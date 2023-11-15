<?php

namespace Framework\Exception;

use Exception;

class InvalidUserException extends Exception
{
    public function __construct($message = "invalid user")
    {
        parent::__construct($message, "0011");
    }
}
