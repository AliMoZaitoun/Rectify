<?php

namespace App\Exceptions\V1\Engineer\Attendance;

use Exception;
use Throwable;

class ShiftTimeoutException extends Exception
{
    public function __construct(
        string $date,
        string $messageKey = "messages.attendance.shift_timeout",
        int $code = 422,
        Throwable $previous = null
    ) {
        $message = __($messageKey, ['date' => $date]);

        parent::__construct($message, $code, $previous);
    }
}
