<?php

namespace App\Exceptions\V1\AI;

use Exception;
use Throwable;

class AiParsingException extends Exception
{
    public function __construct($messageKey = "messages.ai.parsing_failed", $code = 500, Throwable $previous = null)
    {
        $message = __($messageKey);
        parent::__construct($message, $code, $previous);
    }
}
