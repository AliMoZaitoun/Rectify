<?php

namespace App\Http\Requests\V1\AI;

use App\DTOs\AI\AiPolicyDTO;
use App\Enums\AiToneOfVoice;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateAiPolicyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tone_of_voice'           => ['required', new Enum(AiToneOfVoice::class)],
            'legal_guidelines'        => ['nullable', 'string', 'max:2000'],
            'compensation_guidelines' => ['nullable', 'string', 'max:2000'],
            'general_instructions'    => ['nullable', 'string', 'max:2000'],
            'is_active'               => ['boolean'],
        ];
    }

    public function toDTO(): AiPolicyDTO
    {
        return AiPolicyDTO::fromRequest($this);
    }
}
