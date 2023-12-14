<?php

declare(strict_types=1);

namespace Framework\Exception;

use Exception;

class InvalidUserException extends Exception
{
    /**
     * __construct
     *
     * @return void
     */
    public function __construct(string $message = "Opération Impossible<br>Vous n'avez pas les droits pour effectuer cette opération. ")
    {
        parent::__construct($message, 0011);
    }
    // end __construct()
}
