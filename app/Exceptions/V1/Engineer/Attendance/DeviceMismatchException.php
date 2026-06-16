<?php

namespace App\Exceptions\V1\Engineer\Attendance;

use Exception;
use Throwable;

class DeviceMismatchException extends Exception
{
    public function __construct(
        string $messageKey = "messages.attendance.different_device",
        int $code = 422,
        Throwable $previous = null
    ) {
        $message = __($messageKey);

        parent::__construct($message, $code, $previous);
    }
}
