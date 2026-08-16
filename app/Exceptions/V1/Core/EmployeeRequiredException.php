<?php

namespace App\Exceptions\V1\Core;

use Exception;
use Throwable;

class EmployeeRequiredException extends Exception
{
    public function __construct($messageKey = "messages.core.employee_required", $code = 403, Throwable $previous = null)
    {
        $message = __($messageKey);
        parent::__construct($message, $code, $previous);
    }
}
