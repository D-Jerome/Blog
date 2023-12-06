<?php

namespace Framework\Exception;

use Exception;

class DisableUserException extends Exception
{

    /**
     * __construct
     *
     * @return void
     */
    public function __construct(string $message = "disable user")
    {
        parent::__construct($message, 0010);

    }//end __construct()


}
