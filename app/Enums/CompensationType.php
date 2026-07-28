<?php

namespace App\Enums;

enum CompensationType: string
{
    case POINTS = 'points';
    case COUPON = 'coupon';
    case OTHER  = 'other';

    public function label(): string
    {
        return __("labels.compensation_type.{$this->value}");
    }
}
