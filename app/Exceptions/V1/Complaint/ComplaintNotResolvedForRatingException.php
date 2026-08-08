<?php

namespace App\Exceptions\V1\Complaint;

use Exception;
use Throwable;

class ComplaintNotResolvedForRatingException extends Exception
{
    public function __construct($messageKey = "messages.complaint.cannot_rate_unresolved", $code = 422, Throwable $previous = null)
    {
        parent::__construct(__($messageKey), $code, $previous);
    }
}
