<?php

namespace App\DAO\Core;

use Spatie\Activitylog\Models\Activity;

class ActivityLogDAO
{
    public function paginate(array $filters = [], int $perPage = 15)
    {
        return Activity::with(['causer', 'subject'])
            ->when(isset($filters['causer_id']), function ($q) use ($filters) {
                $q->where('causer_id', $filters['causer_id']);
            })
            ->when(isset($filters['event']), function ($q) use ($filters) {
                $q->where('event', $filters['event']);
            })
            ->when(isset($filters['subject_type']), function ($q) use ($filters) {
                $q->where('subject_type', 'like', '%' . $filters['subject_type'] . '%');
            })
            ->when(isset($filters['date_from']), function ($q) use ($filters) {
                $q->whereDate('created_at', '>=', $filters['date_from']);
            })
            ->when(isset($filters['date_to']), function ($q) use ($filters) {
                $q->whereDate('created_at', '<=', $filters['date_to']);
            })
            ->latest()
            ->paginate($perPage);
    }
}
