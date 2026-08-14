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


    public function analyzeNewComplaint(string $title, string $description, array $availableCategories): array
    {
        $categoriesJson = json_encode($availableCategories, JSON_UNESCAPED_UNICODE);

        $prompt = "
        Analyze the following customer complaint.
        Perform the following tasks based strictly on the text's tone, sentiment, and content:
        1. Priority: Determine priority (low, medium, high, urgent). 'urgent' is for severe safety/legal issues or extreme anger.
        2. Spam Detection: Determine if this is spam or gibberish (true/false).
        3. Summary: Provide a very brief 1-sentence summary of the issue in BOTH Arabic and English.
        4. Category Match: From the provided categories JSON, return the ID of the best matching category. If none match, return null.

        [AVAILABLE CATEGORIES]
        {$categoriesJson}

        [COMPLAINT DETAILS]
        - Title: {$title}
        - Description: {$description}

        Format your response EXACTLY as the following JSON structure:
        {
            \"priority\": \"medium\",
            \"is_spam\": false,
            \"ai_summary\": {
                \"ar\": \"الملخص بالعربية هنا...\",
                \"en\": \"Brief summary in English here...\"
            },
            \"ai_suggested_category\": 1
        }
        ";

        try {
            $result = $this->geminiCore->generateJson($prompt);

            $validPriorities = ['low', 'medium', 'high', 'urgent'];
            $priority = strtolower($result['priority'] ?? 'medium');
            $result['priority'] = in_array($priority, $validPriorities) ? $priority : 'medium';

            return $result;
        } catch (\Exception $e) {
            return [
                'priority' => 'medium',
                'is_spam' => false,
                'ai_summary' => null,
                'ai_suggested_category' => null,
            ];
        }
    }

    public function analyzePriority(string $title, string $description): string
    {
        $prompt = "
        Analyze the following customer complaint and determine its priority level based strictly on its content and sentiment.
        The priority MUST be one of these exact lowercase values: 'low', 'medium', 'high', 'urgent'.
        
        Guidelines:
        - 'urgent': Severe safety hazards, severe legal threats, racism, physical harm, or extreme brand damage.
        - 'high': Severe service failure, very angry customer, threats to boycott or churn.
        - 'medium': Standard complaints, moderate delays, normal quality issues, slight frustration.
        - 'low': Suggestions, minor inquiries, slight inconveniences with a calm tone.

        [COMPLAINT DETAILS]
        - Title: {$title}
        - Description: {$description}

        Format your response EXACTLY as the following JSON structure:
        {
            \"priority\": \"value\"
        }
        ";

        try {
            $result = $this->geminiCore->generateJson($prompt);
            $priority = strtolower($result['priority'] ?? 'medium');

            $validPriorities = ['low', 'medium', 'high', 'urgent'];

            return in_array($priority, $validPriorities) ? $priority : 'medium';
        } catch (\Exception $e) {
            return 'medium';
        }
    }

    private function buildReplyPrompt($complaint, $policy): string
    {
        $clientContext = "This is an Anonymous complaint. Focus solely on the severity and tone of the issue to suggest compensation.";

        if (!$complaint->is_anonymous && $complaint->client_id) {
            $points = $complaint->client->points ?? 0;
            $previousComplaintsCount = $complaint->client->complaints()->count();

            $clientContext = "
            This is a Registered Client.
            - Previous Complaints Count: {$previousComplaintsCount}
            - Current Points Balance: {$points}
            Consider their history when suggesting compensation. Do not over-compensate if they complain too frequently.
            ";
        }

        return "
        You are an expert customer service and loyalty retention manager.
        Draft a professional reply to the customer and suggest a fair points compensation amount.

        [COMPANY POLICIES]
        - Tone of Voice: {$policy->tone_of_voice->label()}
        - Legal Boundaries: {$policy->legal_guidelines}
        - Compensation Policy: {$policy->compensation_guidelines}

        [CLIENT CONTEXT]
        {$clientContext}

        [COMPLAINT DETAILS]
        - Title: {$complaint->title}
        - Description: {$complaint->description}

        Based on the SENTIMENT and TONE of the complaint, provide a suggested response and compensation. 
        Format EXACTLY as the following JSON structure:
        {
            \"reply_ar\": \"Suggested reply in Arabic\",
            \"reply_en\": \"Suggested reply in English\",
            \"suggested_compensation_points\": 50,
            \"analysis\": \"Explain why you suggested this points amount and reply, specifically referencing the customer's sentiment (tone) and their history if applicable.\"
        }
        ";
    }
}
