<?php

namespace App\Exceptions\V1\Complaint;

use Exception;
use Throwable;

class CannotDeleteGrantedCompensationException extends Exception
{
    public function __construct($messageKey = "messages.complaint.cannot_delete_granted_compensation", $code = 422, Throwable $previous = null)
    {
        $message = __($messageKey);

        parent::__construct($message, $code, $previous);
    }
}
