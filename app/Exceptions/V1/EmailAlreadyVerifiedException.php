<?php

namespace App\Exceptions\V1;

use Exception;
use Throwable;

class EmailAlreadyVerifiedException extends Exception
{
    public function __construct($messageKey = "messages.auth.already_verified", $code = 400, Throwable $previous = null)
    {
        $message = __($messageKey);

        parent::__construct($message, $code, $previous);
    }
}
