<?php

namespace App\Events;

use App\Models\Complaint\Complaint;
use App\Models\Complaint\ComplaintAction;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ComplaintReplyAdded
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Complaint $complaint,
        public mixed $reply
    ) {}
}
