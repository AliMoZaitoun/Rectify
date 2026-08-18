<?php

namespace App\Exceptions\V1\Client;

use Exception;
use Throwable;

class ClientRequiredForDirectCompensationException extends Exception
{
    public function __construct($messageKey = "messages.compensations.client_required", $code = 422, Throwable $previous = null)
    {
        $message = __($messageKey);

        parent::__construct($message, $code, $previous);
    }
}
