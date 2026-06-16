<?php

namespace App\Exceptions\V1\Sales;

use Exception;
use Throwable;

class CompleteFutureAppointmentException extends Exception
{
    public function __construct(
        string $messageKey = "messages.appointment.cannot_complete_future_appointment",
        int $code = 422,
        Throwable $previous = null
    ) {
        $message = __($messageKey);

        parent::__construct($message, $code, $previous);
    }
}
