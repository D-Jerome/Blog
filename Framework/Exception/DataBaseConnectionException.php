<?php

declare(strict_types=1);

namespace Framework\Exception;

use Exception;

class DataBaseConnectionException extends Exception
{
    /**
     * __construct
     */
    public function __construct(string $message = "database connection doesn't work")
    {
        parent::__construct($message, 0000);
    }
    // end __construct()
}
