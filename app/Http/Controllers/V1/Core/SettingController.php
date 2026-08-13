<?php

namespace App\Http\Controllers\V1\Core;

use App\Http\Controllers\Controller;
use App\Services\Core\SettingService;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    use ResponseTrait;

    public function __construct(
        private SettingService $settingService
    ) {}

    public function getCompensationLimits()
    {
        $limits = $this->settingService->getCompensationLimits();
        return $this->successResponse($limits);
    }

    public function updateCompensationLimits(Request $request)
    {
        $data = $request->validate([
            'employee_compensation_limit' => ['nullable', 'numeric', 'min:0'],
            'manager_compensation_limit'  => ['nullable', 'numeric', 'min:0'],
        ]);

        $this->settingService->updateCompensationLimits($data);

        return $this->successResponse(null, __('messages.common.updated'));
    }
}
