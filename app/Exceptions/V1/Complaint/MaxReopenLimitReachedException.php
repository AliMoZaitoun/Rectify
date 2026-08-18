<?php

namespace App\Exceptions\V1\Complaint;

use Exception;
use Throwable;

class MaxReopenLimitReachedException extends Exception
{
    public function __construct($messageKey = "messages.complaint.max_reopens_reached", $code = 422, Throwable $previous = null)
    {
        $message = __($messageKey);

        parent::__construct($message, $code, $previous);
    }
}
