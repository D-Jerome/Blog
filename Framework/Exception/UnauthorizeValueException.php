<?php

namespace Framework\Exception;

use Exception;

class UnauthorizeValueException extends Exception
{
    public function __construct($message = "Unauthorize values")
    {
        parent::__construct($message, "0009");
    }
}
