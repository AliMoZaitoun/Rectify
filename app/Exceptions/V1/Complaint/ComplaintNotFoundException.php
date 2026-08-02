<?php

namespace App\Exceptions\V1\Complaint;

use Exception;
use Throwable;

class ComplaintNotFoundException extends Exception
{
    public function __construct($messageKey = "messages.complaint.complaint_not_found", $code = 404, Throwable $previous = null)
    {
        $message = __($messageKey);

        parent::__construct($message, $code, $previous);
    }
}
