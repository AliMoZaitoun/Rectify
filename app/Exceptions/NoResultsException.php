<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class NoResultsException extends Exception
{
    public function __construct($messageKey = "messages.system.no_results", $code = 200, Throwable $previous = null)
    {
        $message = __($messageKey);

        parent::__construct($message, $code, $previous);
    }
}
