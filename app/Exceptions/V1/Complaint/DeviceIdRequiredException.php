<?php

namespace App\Exceptions\V1\Complaint;

use Exception;
use Throwable;

class DeviceIdRequiredException extends Exception
{
    public function __construct($messageKey = "messages.complaint.device_id_required", $code = 422, Throwable $previous = null)
    {
        $message = __($messageKey);

        parent::__construct($message, $code, $previous);
    }
}
