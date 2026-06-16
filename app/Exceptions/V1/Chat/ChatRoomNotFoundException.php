<?php

namespace App\Exceptions\V1\Chat;

use Exception;
use Throwable;

class ChatRoomNotFoundException extends Exception
{
    public function __construct(
        $messageKey = "messages.chat.room_not_found",
        $code = 404,
        Throwable $previous = null
    ) {
        $message = __($messageKey);

        parent::__construct($message, $code, $previous);
    }
}
