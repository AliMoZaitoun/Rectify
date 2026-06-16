<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class UnitAlreadyFavoritedException extends Exception
{
    public function __construct($messageKey = "messages.favorites.already_exists", $code = 422, Throwable $previous = null)
    {
        $message = __($messageKey);

        parent::__construct($message, $code, $previous);
    }
}
