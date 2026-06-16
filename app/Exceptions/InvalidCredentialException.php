<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class InvalidCredentialException extends Exception
{
    public function __construct($messageKey = "messages.auth.invalid", $code = 401, Throwable $previous = null)
    {
        $message = __($messageKey);

        parent::__construct($message, $code, $previous);
    }
}
