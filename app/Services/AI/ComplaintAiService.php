<?php

namespace App\Services\AI;

use App\DAO\Complaint\ComplaintDAO;
use App\Exceptions\V1\AI\AiAssistantDisabledException;
use App\Exceptions\V1\AI\AiResponseGenerationException;
use App\Exceptions\V1\Complaint\ComplaintNotFoundException;
use Exception;

class ComplaintAiService
{
    public function __construct(
        private readonly GeminiCoreService $geminiCore,
        private readonly AiPolicyService $policyService,
        private readonly ComplaintDAO $complaintDAO
    ) {}

    public function suggestReply(int $complaintId): array
    {
        $complaint = $this->complaintDAO->byId($complaintId);

        if (!$complaint) {
            throw new ComplaintNotFoundException();
        }

        $policy = $this->policyService->getAiPolicy();

        if (!$policy || !$policy->is_active) {
            throw new AiAssistantDisabledException();
        }

        $prompt = $this->buildReplyPrompt($complaint, $policy);

        try {
            return $this->geminiCore->generateJson($prompt);
        } catch (\Exception $e) {
            throw new AiResponseGenerationException();
        }
    }

    private function buildReplyPrompt($complaint, $policy): string
    {
        return "
        You are an expert customer service representative working for our company.
        Your task is to draft a professional reply to a customer's complaint based on our strict company policies.

        [COMPANY POLICIES & GUIDELINES]
        - Tone of Voice: {$policy->tone_of_voice->label()}
        - Legal Boundaries (Do NOT say these): {$policy->legal_guidelines}
        - Compensation Policy: {$policy->compensation_guidelines}
        - General Instructions: {$policy->general_instructions}

        [COMPLAINT DETAILS]
        - Title: {$complaint->title}
        - Description: {$complaint->description}
        - Category: " . ($complaint->category ? $complaint->category->name : 'غير محدد') . "

        Based on the above, provide a suggested response to the customer. 
        Format your response EXACTLY as the following JSON structure:
        {
            \"reply_ar\": \"Suggested reply in Arabic\",
            \"reply_en\": \"Suggested reply in English\",
            \"analysis\": \"A brief 1-sentence internal note explaining why you chose this response based on the policy.\"
        }
        ";
    }
}
