<?php

namespace App\Services\AI;

use App\DAO\AI\AiPolicyDAO;
use App\DTOs\AI\AiPolicyDTO;
use App\Models\AI\AiPolicy;

class AiPolicyService
{
    public function __construct(
        private readonly AiPolicyDAO $aiPolicyDAO
    ) {}

    public function getAiPolicy(): ?AiPolicy
    {
        return $this->aiPolicyDAO->getPolicy();
    }

    public function updateAiPolicy(AiPolicyDTO $dto): AiPolicy
    {
        return $this->aiPolicyDAO->updateOrCreatePolicy($dto->toArray());
    }
}
