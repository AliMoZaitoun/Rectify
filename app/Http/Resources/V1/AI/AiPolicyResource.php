<?php

namespace App\Http\Resources\V1\AI;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AiPolicyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                      => $this->id,
            'tone_of_voice'           => $this->tone_of_voice->value,
            'legal_guidelines'        => $this->legal_guidelines,
            'compensation_guidelines' => $this->compensation_guidelines,
            'general_instructions'    => $this->general_instructions,
            'is_active'               => (bool) $this->is_active,
            'updated_at'              => $this->updated_at?->toIso8601String(),
        ];
    }
}
