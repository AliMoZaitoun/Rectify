<?php

namespace App\Exceptions\V1\Engineer\Attendance;

use Exception;
use Throwable;

class InvalidCheckOutTimeException extends Exception
{
    public function __construct(
        string $messageKey = "messages.attendance.invalid_checkout_time",
        int $code = 422,
        Throwable $previous = null
    ) {
        $message = __($messageKey);

        parent::__construct($message, $code, $previous);
    }
}
