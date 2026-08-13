<?php

namespace App\Models\AI;

use App\Enums\AiToneOfVoice;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'tone_of_voice',
    'legal_guidelines',
    'compensation_guidelines',
    'general_instructions',
    'is_active',
])]

class AiPolicy extends Model
{
    protected $table = 'ai_policies';

    protected $casts = [
        'is_active'     => 'boolean',
        'tone_of_voice' => AiToneOfVoice::class,
    ];
}
