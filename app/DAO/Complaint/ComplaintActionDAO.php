<?php

namespace App\DAO\Complaint;

use App\DTOs\Complaint\ComplaintActionDTO;
use App\Models\Complaint\ComplaintAction;

class ComplaintActionDAO
{
    public function store(ComplaintActionDTO $dto): ComplaintAction
    {
        return ComplaintAction::create($dto->toArray());
    }
}
