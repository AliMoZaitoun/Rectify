<?php

namespace App\Exceptions\V1\Complaint;

use Exception;
use Throwable;

class CannotReopenComplaintException extends Exception
{
    public function __construct($messageKey = "messages.complaint.cannot_reopen", $code = 422, Throwable $previous = null)
    {
        parent::__construct(__($messageKey), $code, $previous);
    }
}
