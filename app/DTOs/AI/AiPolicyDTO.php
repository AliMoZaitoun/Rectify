<?php

namespace App\DTOs\AI;

use App\Enums\AiToneOfVoice;
use Illuminate\Http\Request;

class AiPolicyDTO
{
    public function __construct(
        public readonly AiToneOfVoice $tone_of_voice,
        public readonly ?string $legal_guidelines,
        public readonly ?string $compensation_guidelines,
        public readonly ?string $general_instructions,
        public readonly bool $is_active
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            tone_of_voice: AiToneOfVoice::from($request->validated('tone_of_voice')),
            legal_guidelines: $request->validated('legal_guidelines'),
            compensation_guidelines: $request->validated('compensation_guidelines'),
            general_instructions: $request->validated('general_instructions'),
            is_active: $request->validated('is_active', true)
        );
    }

    public function toArray(): array
    {
        return [
            'tone_of_voice'           => $this->tone_of_voice->value,
            'legal_guidelines'        => $this->legal_guidelines,
            'compensation_guidelines' => $this->compensation_guidelines,
            'general_instructions'    => $this->general_instructions,
            'is_active'               => $this->is_active,
        ];
    }
}
