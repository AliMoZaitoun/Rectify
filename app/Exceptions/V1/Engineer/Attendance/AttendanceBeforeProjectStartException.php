<?php

namespace App\Exceptions\V1\Engineer\Attendance;

use Exception;
use Throwable;

class AttendanceBeforeProjectStartException extends Exception
{
    public function __construct(
        string $startDate,
        string $messageKey = "messages.attendance.before_project_start",
        int $code = 422,
        Throwable $previous = null
    ) {
        $message = __($messageKey, ['date' => $startDate]);

        parent::__construct($message, $code, $previous);
    }
}
