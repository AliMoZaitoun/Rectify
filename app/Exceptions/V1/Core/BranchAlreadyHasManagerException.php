<?php

namespace App\Exceptions\V1\Core;

use Exception;
use Throwable;

class BranchAlreadyHasManagerException extends Exception
{
    public function __construct($messageKey = "messages.branches.already_has_manager", $code = 422, Throwable $previous = null)
    {
        $message = __($messageKey);

        parent::__construct($message, $code, $previous);
    }
}
