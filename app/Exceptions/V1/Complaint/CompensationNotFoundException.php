<?php

namespace App\Exceptions\V1\Complaint;

use Exception;
use Throwable;

class CompensationNotFoundException extends Exception
{
    public function __construct($messageKey = "messages.common.not_found", $code = 404, Throwable $previous = null)
    {
        $message = __($messageKey);

        parent::__construct($message, $code, $previous);
    }
}
