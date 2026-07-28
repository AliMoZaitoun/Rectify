<?php

namespace App\Enums;

enum CompensationStatus: string
{
    case PENDING  = 'pending';
    case GRANTED  = 'granted';
    case REJECTED = 'rejected';

    public function label(): string
    {
        return __("labels.compensation_status.{$this->value}");
    }
}
