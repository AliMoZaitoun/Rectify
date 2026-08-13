<?php

namespace App\Exceptions\V1\AI;

use Exception;
use Throwable;

class AiAssistantDisabledException extends Exception
{
    public function __construct($messageKey = "messages.ai.disabled", $code = 403, Throwable $previous = null)
    {
        $message = __($messageKey);
        parent::__construct($message, $code, $previous);
    }
}
