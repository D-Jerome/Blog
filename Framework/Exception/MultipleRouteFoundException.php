<?php

declare(strict_types=1);

namespace Framework\Exception;

use Exception;

class MultipleRouteFoundException extends Exception
{
    /**
     * __construct
     */
    public function __construct(string $message = 'More than 1 route has been found')
    {
        parent::__construct($message, 0001);
    }
    // end __construct()
}
