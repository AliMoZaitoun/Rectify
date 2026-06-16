<?php

namespace App\Exceptions\V1;

use Exception;
use Throwable;

class InvalidRefreshTokenException extends Exception
{
    public function __construct($messageKey = "messages.auth.invalid_refresh_token", $code = 401, Throwable $previous = null)
    {
        $message = __($messageKey);

        parent::__construct($message, $code, $previous);
    }
}
