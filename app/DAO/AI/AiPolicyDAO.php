<?php

namespace App\DAO\AI;

use App\Models\AI\AiPolicy;

class AiPolicyDAO
{
    public function getPolicy(): ?AiPolicy
    {
        return AiPolicy::first();
    }

    public function updateOrCreatePolicy(array $data): AiPolicy
    {
        $policy = $this->getPolicy();

        if ($policy) {
            $policy->update($data);
            return $policy;
        }

        return AiPolicy::create($data);
    }
}
