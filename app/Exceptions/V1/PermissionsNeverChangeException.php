<?php

namespace App\Exceptions\V1;

use Exception;
use Throwable;

class PermissionsNeverChangeException extends Exception
{
    public function __construct($messageKey = "messages.system.permission_not_change", $code = 403, Throwable $previous = null)
    {
        $message = __($messageKey);

        parent::__construct($message, $code, $previous);
    }
}
