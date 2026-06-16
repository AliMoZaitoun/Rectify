<?php

namespace App\Exceptions\V1\Engineer\Attendance;

use Exception;
use Throwable;

class AlreadyCheckedInException extends Exception
{
    public function __construct(
        string $time,
        string $messageKey = "messages.attendance.already_checked_in",
        int $code = 422,
        Throwable $previous = null
    ) {
        $message = __($messageKey, ['time' => $time]);

        parent::__construct($message, $code, $previous);
    }
}
