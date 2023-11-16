<?php

namespace Framework\Exception;

use Exception;

class PropertyNotFoundException extends Exception
{

    /**
     * __construct
     *
     * @param  string $message
     * @return void
     */
    public function __construct(string $message = "Property has not been found")
    {
        parent::__construct($message, 0003);
    }//end __construct

}
