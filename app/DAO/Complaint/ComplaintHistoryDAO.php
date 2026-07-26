<?php

namespace App\DAO\Complaint;

use App\DTOs\Complaint\Create\ComplaintHistoryDTO;
use App\Models\Complaint\ComplaintHistory;

class ComplaintHistoryDAO
{
    public function store(ComplaintHistoryDTO $dto): ComplaintHistory
    {
        return ComplaintHistory::create($dto->toArray());
    }
}
