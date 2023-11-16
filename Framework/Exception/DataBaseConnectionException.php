<?php

namespace Framework\Exception;

use Exception;

class DataBaseConnectionException extends Exception
{

    /**
     * __construct
     *
     * @param  mixed $message
     * @return void
     */
    public function __construct($message = "database connection doesn't work")
    {
        parent::__construct($message, "0000");
    }//end __construct

}
