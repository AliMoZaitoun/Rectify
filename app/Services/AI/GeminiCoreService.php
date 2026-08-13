<?php

namespace App\Services\AI;

use App\Exceptions\V1\AI\AiConnectionException;
use App\Exceptions\V1\AI\AiParsingException;
use Gemini;
use Illuminate\Support\Facades\Log;

class GeminiCoreService
{
    protected $client;
    protected string $defaultModel = 'gemini-2.5-flash';

    public function __construct()
    {
        $apiKey = config('services.gemini.key');
        $this->client = Gemini::client($apiKey);
    }

    public function generateText(string $prompt, ?string $modelName = null): string
    {
        try {
            $result = $this->client
                ->generativeModel($modelName ?? $this->defaultModel)
                ->generateContent($prompt);

            return $result->text();
        } catch (\Exception $e) {
            Log::error('Gemini API Error: ' . $e->getMessage());

            throw new AiConnectionException();
        }
    }

    public function generateJson(string $prompt, ?string $modelName = null): array
    {
        $prompt .= "\n\nIMPORTANT: Respond ONLY with a valid JSON object. Do not include any markdown formatting like ```json or ```.";

        $aiText = $this->generateText($prompt, $modelName);

        $cleanJson = preg_replace('/^```json\s*|\s*```$/i', '', trim($aiText));

        $decoded = json_decode($cleanJson, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('Gemini JSON Parse Error: ' . json_last_error_msg() . ' | Raw Output: ' . $aiText);

            throw new AiParsingException();
        }

        return $decoded;
    }
}
