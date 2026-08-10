<?php

namespace App\Http\Controllers\V1;

use App\DTOs\DeviceTokenData;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\RegisterDeviceTokenRequest;
use App\Http\Resources\V1\DeviceTokenResource;
use App\Services\DeviceTokenService;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class DeviceTokenController extends Controller
{
    use ResponseTrait;
    public function __construct(
        protected DeviceTokenService $deviceTokenService
    ) {}

    public function store(RegisterDeviceTokenRequest $request)
    {
        $dto = DeviceTokenData::fromRequest(
            userId: $request->user()->id,
            validated: $request->validated()
        );

        $deviceToken = $this->deviceTokenService->registerToken($dto);

        return $this->useResource($deviceToken, DeviceTokenResource::class, __('messages.common.stored'));
    }

    public function destroy(Request $request)
    {
        $request->validate(['fcm_token' => 'required|string']);

        $this->deviceTokenService->removeToken(
            userId: $request->user()->id,
            fcmToken: $request->fcm_token
        );

        return $this->successResponse([], __('messages.common.deleted'));
    }
}
