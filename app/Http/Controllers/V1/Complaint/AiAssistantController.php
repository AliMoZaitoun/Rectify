<?php

namespace App\Http\Controllers\V1\Complaint;

use App\Http\Controllers\Controller;
use App\Services\AI\ComplaintAiService;
use App\Traits\ResponseTrait;

class AiAssistantController extends Controller
{
    use ResponseTrait;

    public function __construct(
        private readonly ComplaintAiService $aiService
    ) {}

    public function suggestReply(int $id)
    {
        $suggestion = $this->aiService->suggestReply($id);

        return $this->successResponse($suggestion, __('messages.ai.generated_successfully'));
    }
}
