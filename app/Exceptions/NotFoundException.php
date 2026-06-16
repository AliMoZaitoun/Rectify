<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class NotFoundException extends \RuntimeException
{
    public function __construct($entityKey = 'messages.common.resource', $code = 404, Throwable $previous = null)
    {
        $entityName = __($entityKey);
        $message = __('messages.common.not_found_item', ['item' => $entityName]);

        parent::__construct($message, $code, $previous);
    }
}
