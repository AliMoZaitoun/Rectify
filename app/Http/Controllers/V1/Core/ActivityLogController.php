<?php

namespace App\Http\Controllers\V1\Core;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\Core\ActivityLogResource;
use App\Services\Core\ActivityLogService;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    use ResponseTrait;

    public function __construct(
        private ActivityLogService $activityLogService
    ) {}

    public function index(Request $request)
    {
        $filters = $request->only([
            'causer_id',
            'event',
            'subject_type',
            'date_from',
            'date_to'
        ]);

        $logs = $this->activityLogService->getAll(
            filters: $filters,
            perPage: $request->integer('per_page', 20)
        );

        return $this->successCollection($logs, ActivityLogResource::class);
    }
}
