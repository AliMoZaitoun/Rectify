<?php

namespace App\Services\Core;

use App\DAO\Core\ActivityLogDAO;

class ActivityLogService
{
    public function __construct(
        private ActivityLogDAO $activityLogDAO
    ) {}

    public function getAll(array $filters = [], int $perPage = 15)
    {
        return $this->activityLogDAO->paginate($filters, $perPage);
    }
}
