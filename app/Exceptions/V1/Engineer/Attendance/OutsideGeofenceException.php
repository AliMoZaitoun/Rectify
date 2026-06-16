<?php

namespace App\Exceptions\V1\Engineer\Attendance;

use Exception;
use Throwable;

class OutsideGeofenceException extends Exception
{
    public function __construct(
        int $distance,
        string $messageKey = "messages.attendance.outside_geofence",
        int $code = 422,
        Throwable $previous = null
    ) {
        $message = __($messageKey, ['distance' => $distance]);

        parent::__construct($message, $code, $previous);
    }
}
