<?php

namespace App\Exceptions\V1;

use Exception;
use Throwable;

class ProjectHasNoBuildingsException extends Exception
{
    public function __construct(
        string $messageKey = "messages.attendance.project_has_no_buildings",
        int $code = 422,
        Throwable $previous = null
    ) {
        $message = __($messageKey);
        parent::__construct($message, $code, $previous);
    }
}
