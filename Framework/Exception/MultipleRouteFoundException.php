<?php

namespace Framework\Exception;

use Exception;

class MultipleRouteFoundException extends Exception
{

    /**
     * __construct
     *
     * @param  string $message
     * @return void
     */
    public function __construct(string $message = "More than 1 route has been found")
    {
        parent::__construct($message, 0001);
    }//end __construct

}
