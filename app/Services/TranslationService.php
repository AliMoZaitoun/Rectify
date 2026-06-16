<?php

namespace App\Services;

use Gemini;

class TranslationService
{
    protected $client;

    public function __construct()
    {
        $apiKey = config('services.gemini.key');
        $this->client = Gemini::client($apiKey);
    }

    public function translateAll(string $text): array
    {
        $modelName = 'gemini-2.5-flash';

        $result = $this->client
            ->generativeModel($modelName)
            ->generateContent($this->buildPrompt($text));

        $aiText = $result->text();
        return json_decode($aiText, true) ?? ['ar' => $text];
    }

    private function buildPrompt(string $text): string
    {
        $targetLanguages = ['ar', 'en'];

        $prompt = "You are an expert real estate and architecture translator. 
                   Translate the following text into these languages: " . implode(', ', $targetLanguages) . ". 
                   Respond ONLY with a valid JSON object where keys are the language codes and values are the translations.
                   Do not include any markdown formatting or markdown code blocks (like ```json).
                   Text to translate: \"{$text}\"";

        return $prompt;
    }
}
