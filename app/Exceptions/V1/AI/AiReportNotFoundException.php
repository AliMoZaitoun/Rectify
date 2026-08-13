<?php

namespace App\Exceptions\V1\AI;

use Exception;
use Throwable;

class AiReportNotFoundException extends Exception
{
    public function __construct($messageKey = "messages.ai.report_not_found", $code = 404, Throwable $previous = null)
    {
        $message = __($messageKey);
        parent::__construct($message, $code, $previous);
    }
}
