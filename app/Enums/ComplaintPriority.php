<?php

namespace App\Enums;

enum ComplaintPriority: string
{
    case LOW    = 'low';
    case MEDIUM = 'medium';
    case HIGH   = 'high';
    case URGENT = 'urgent';

    public function label(): string
    {
        return __("labels.complaint_priority.{$this->value}");
    }
}
