<?php

namespace App\DTOs\Complaint;

use App\Http\Requests\V1\Complaint\ReopenComplaintRequest;

class ReopenComplaintDTO
{
    public function __construct(
        public readonly string $complaintCode,
        public readonly string $reason,
        public readonly ?int $actorId = null,
        public readonly ?string $actorType = null
    ) {}

    public static function fromRequest(ReopenComplaintRequest $request, string $complaintCode, ?int $actorId = null, ?string $actorType = null): self
    {
        return new self(
            complaintCode: $complaintCode,
            reason: $request->validated('reason'),
            actorId: $actorId,
            actorType: $actorType
        );
    }
}
