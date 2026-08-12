<?php

namespace App\Exceptions\V1\Complaint;

use Exception;
use Throwable;

class CannotMergeParentComplaintException extends Exception
{
    public function __construct(
        $messageKey = "messages.complaint.cannot_merge_parent_complaint",
        $code = 422,
        Throwable $previous = null
    ) {
        $message = __($messageKey);

        parent::__construct($message, $code, $previous);
    }
}
