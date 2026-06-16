<?php

namespace App\Exceptions;

use Throwable;

class OtpCodeInvalidException extends \RuntimeException
{
    public function __construct($messageKey = "messages.auth.otp_invalid", $code = 401, Throwable $previous = null)
    {
        $message = __($messageKey);

        parent::__construct($message, $code, $previous);
    }
}
