<?php

namespace App\Exceptions\V1\Engineer\Attendance;

use Exception;
use Throwable;

class LowGpsAccuracyException extends Exception
{
    public function __construct(
        int $currentAccuracy,
        int $requiredAccuracy,
        string $messageKey = "messages.attendance.low_gps_accuracy",
        int $code = 422,
        Throwable $previous = null
    ) {
        $message = __($messageKey, [
            'current' => $currentAccuracy,
            'required' => $requiredAccuracy
        ]);

        parent::__construct($message, $code, $previous);
    }
}
