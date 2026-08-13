<?php

namespace App\Enums;

enum AiToneOfVoice: string
{
    case PROFESSIONAL = 'professional';
    case EMPATHETIC   = 'empathetic';
    case STRICT       = 'strict';
    case FRIENDLY     = 'friendly';

    public function label(): string
    {
        return __("labels.ai_tone.{$this->value}");
    }
}
