<?php

namespace Framework\Exception;

use Exception;

class UnauthorizeValueException extends Exception
{

    /**
     * __construct
     *
     * @param  string $message
     * @return void
     */
    public function __construct(string $message = "Unauthorize values")
    {
        parent::__construct($message, 006);

    }//end __construct()


}
