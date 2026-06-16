<?php

namespace App\Exceptions\V1;

use Exception;
use Throwable;

class InvalidPasswordException extends Exception
{
    public function __construct($messageKey = "messages.auth.password_invalid", $code = 422, Throwable $previous = null)
    {
        $message = __($messageKey);

        parent::__construct($message, $code, $previous);
    }
}
