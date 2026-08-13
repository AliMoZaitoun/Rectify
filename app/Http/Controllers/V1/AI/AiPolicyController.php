<?php

namespace App\Http\Controllers\V1\AI;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\AI\UpdateAiPolicyRequest;
use App\Http\Resources\V1\AI\AiPolicyResource;
use App\Services\AI\AiPolicyService;
use App\Traits\ResponseTrait;

class AiPolicyController extends Controller
{
    use ResponseTrait;

    public function __construct(
        private readonly AiPolicyService $aiPolicyService
    ) {}

    public function show()
    {
        $policy = $this->aiPolicyService->getAiPolicy();

        if (!$policy) {
            return $this->successResponse(null, __('messages.common.retrieved'));
        }

        return $this->useResource($policy, AiPolicyResource::class, __('messages.common.retrieved'));
    }

    public function update(UpdateAiPolicyRequest $request)
    {
        $policy = $this->aiPolicyService->updateAiPolicy($request->toDTO());

        return $this->useResource($policy, AiPolicyResource::class, __('messages.common.updated'));
    }
}
