<?php

namespace App\Exceptions\V1\Complaint;

use Exception;
use Throwable;

class InvalidCouponException extends Exception
{
    public function __construct($messageKey = "messages.compensations.invalid_coupon", $code = 422, Throwable $previous = null)
    {
        $message = __($messageKey);
        parent::__construct($message, $code, $previous);
    }
}
