<?php

namespace App\Exceptions\V1\Client;

use Exception;
use Throwable;

class ClientNotFoundException extends Exception
{
    public function __construct($messageKey = "messages.client.client_not_found", $code = 404, Throwable $previous = null)
    {
        $message = __($messageKey);
        parent::__construct($message, $code, $previous);
    }
}
