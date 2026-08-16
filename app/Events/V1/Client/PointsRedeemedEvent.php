<?php

namespace App\Events\V1\Client;

use App\Models\Client;
use App\Models\Core\Employee;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PointsRedeemedEvent
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Client $client,
        public int $points,
        public Employee $employee
    ) {}
}
