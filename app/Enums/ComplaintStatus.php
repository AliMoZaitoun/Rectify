<?php

namespace App\Enums;

enum ComplaintStatus: string
{
    case PENDING           = 'pending';
    case IN_PROGRESS       = 'in_progress';
    case WAITING_DOCUMENTS = 'waiting_documents';
    case RESOLVED          = 'resolved';
    case CLOSED            = 'closed';
    case REJECTED          = 'rejected';

    public function label(): string
    {
        return __("labels.complaint_status.{$this->value}");
    }
}
