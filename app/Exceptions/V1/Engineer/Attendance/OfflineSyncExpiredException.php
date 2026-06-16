<?php

namespace App\Exceptions\V1\Engineer\Attendance;

use Exception;
use Throwable;

class OfflineSyncExpiredException extends Exception
{
    public function __construct(
        int $maxDays,
        string $messageKey = "messages.attendance.offline_sync_expired",
        int $code = 422,
        Throwable $previous = null
    ) {
        $message = __($messageKey, ['days' => $maxDays]);

        parent::__construct($message, $code, $previous);
    }
}
