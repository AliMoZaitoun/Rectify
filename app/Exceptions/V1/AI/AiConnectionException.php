<?php

namespace App\Exceptions\V1\AI;

use Exception;
use Throwable;

class AiConnectionException extends Exception
{
    public function __construct($messageKey = "messages.ai.connection_failed", $code = 502, Throwable $previous = null)
    {
        $message = __($messageKey);
        parent::__construct($message, $code, $previous);
    }
}
