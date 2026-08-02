<?php

namespace App\Exceptions\V1\Complaint;

use Exception;
use Throwable;

class UnresolvedComplaintCompensationException extends Exception
{
    public function __construct($messageKey = "messages.complaint.cannot_compensate_unresolved_complaint", $code = 422, Throwable $previous = null)
    {
        $message = __($messageKey);

        parent::__construct($message, $code, $previous);
    }
}
