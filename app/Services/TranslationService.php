<?php

namespace App\Services;

use App\Services\AI\GeminiCoreService;

class TranslationService
{
    public function __construct(
        private readonly GeminiCoreService $geminiCore
    ) {}

    public function translateAll(string $text): array
    {
        $targetLanguages = ['ar', 'en'];
        $langs = implode(', ', $targetLanguages);

        $prompt = "You are an expert translator. Translate the following text into these languages: {$langs}. 
                   Format your response EXACTLY as a JSON object where keys are the language codes and values are the translations.
                   Text to translate: \"{$text}\"";

        try {
            return $this->geminiCore->generateJson($prompt);
        } catch (\Exception $e) {
            return ['ar' => $text, 'en' => $text];
        }
    }
}
