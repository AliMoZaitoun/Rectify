<?php

namespace App\Exceptions\V1\Complaint;

use Exception;
use Throwable;

class ComplaintAlreadyCompensatedException extends Exception
{
    public function __construct($messageKey = "messages.complaint.already_compensated", $code = 422, Throwable $previous = null)
    {
        $message = __($messageKey);

        parent::__construct($message, $code, $previous);
    }
}
