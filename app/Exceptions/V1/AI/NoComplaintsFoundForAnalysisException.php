<?php

namespace App\Exceptions\V1\AI;

use Exception;
use Throwable;

class NoComplaintsFoundForAnalysisException extends Exception
{
    public function __construct($messageKey = "messages.ai.no_complaints_for_report", $code = 404, Throwable $previous = null)
    {
        $message = __($messageKey);
        parent::__construct($message, $code, $previous);
    }
}
