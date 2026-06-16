<?php

namespace App\Exceptions\V1\Engineer\Attendance;

use Exception;
use Throwable;

class FutureAttendanceTimeException extends Exception
{
    public function __construct(
        string $messageKey = "messages.attendance.future_time_detected",
        int $code = 422,
        Throwable $previous = null
    ) {
        $message = __($messageKey);

        parent::__construct($message, $code, $previous);
    }
}
