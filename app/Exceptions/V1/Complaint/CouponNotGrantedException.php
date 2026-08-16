<?php

namespace App\Exceptions\V1\Complaint;

use Exception;
use Throwable;

class CouponNotGrantedException extends Exception
{
    public function __construct($messageKey = "messages.compensations.coupon_not_granted", $code = 422, Throwable $previous = null)
    {
        $message = __($messageKey);
        parent::__construct($message, $code, $previous);
    }
}
