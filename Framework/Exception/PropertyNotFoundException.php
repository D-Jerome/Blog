<?php

declare(strict_types=1);

namespace Framework\Exception;

use Exception;

class PropertyNotFoundException extends Exception
{
    /**
     * __construct
     */
    public function __construct(string $message = 'Property has not been found')
    {
        parent::__construct($message, 0003);
    }
    // end __construct()
}
