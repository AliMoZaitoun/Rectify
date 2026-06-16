<?php

namespace App\Exceptions\V1\Chat;

use Exception;
use Throwable;

class UnauthorizedChatAccessException extends Exception
{
    public function __construct(
        $messageKey = "messages.chat.unauthorized_access",
        $code = 403,
        Throwable $previous = null
    ) {
        $message = __($messageKey);

        parent::__construct($message, $code, $previous);
    }
}
