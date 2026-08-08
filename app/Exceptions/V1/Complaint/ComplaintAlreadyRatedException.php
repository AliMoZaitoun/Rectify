<?php

namespace App\Exceptions\V1\Complaint;

use Exception;
use Throwable;

class ComplaintAlreadyRatedException extends Exception
{
    public function __construct($messageKey = "messages.complaint.already_rated", $code = 422, Throwable $previous = null)
    {
        parent::__construct(__($messageKey), $code, $previous);
    }
}
