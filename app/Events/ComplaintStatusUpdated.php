<?php

namespace App\Events;

use App\Models\Complaint\Complaint;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ComplaintStatusUpdated
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Complaint $complaint,
        public string $oldStatus
    ) {}
}
