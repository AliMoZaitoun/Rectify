<?php

namespace App\Exceptions\V1\Order;

use Exception;
use Throwable;

class OrderAlreadySubmittedException extends Exception
{
    public function __construct($messageKey = "messages.orders.already_submitted", $code = 422, Throwable $previous = null)
    {
        $message = __($messageKey);

        parent::__construct($message, $code, $previous);
    }
}
