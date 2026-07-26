<?php

namespace App\Enums;

enum ComplaintStatus: string
{
    case PENDING = 'pending';
    case IN_PROGRESS = 'in_progress';
    case WAITING_DOCUMENTS = 'waiting_documents';
    case RESOLVED = 'resolved';
    case CLOSED = 'closed';
    case REJECTED = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'قيد الانتظار',
            self::IN_PROGRESS => 'قيد المعالجة',
            self::WAITING_DOCUMENTS => 'بانتظار الوثائق',
            self::RESOLVED => 'محلولة',
            self::CLOSED => 'مغلقة',
            self::REJECTED => 'مرفوضة',
        };
    }
}
