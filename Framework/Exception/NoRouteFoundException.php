<?php

namespace Framework\Exception;

use Exception;

class NoRouteFoundException extends Exception
{
    /**
     * __construct
     *
     * @return void
     */
    public function __construct(string $message = "No route has been found")
    {
        parent::__construct($message, 0002);
    }
    //end __construct()
}
