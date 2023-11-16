<?php

namespace Framework\Exception;

use Exception;

class InvalidUserException extends Exception
{

    /**
     * __construct
     *
     * @param  string $message
     * @return void
     */
    public function __construct(string $message = "invalid user")
    {
        parent::__construct($message, 0011);
    }//end __construct

}
