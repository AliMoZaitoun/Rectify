<?php

namespace App\DTOs\Complaint;

use App\Http\Requests\V1\Complaint\ReopenComplaintRequest;

class ReopenComplaintDTO
{
    public function __construct(
        public readonly int $complaintId,
        public readonly string $reason,
        public readonly ?int $actorId = null,
        public readonly ?string $actorType = null
    ) {}

    public static function fromRequest(ReopenComplaintRequest $request, int $complaintId, ?int $actorId = null, ?string $actorType = null): self
    {
        return new self(
            complaintId: $complaintId,
            reason: $request->validated('reason'),
            actorId: $actorId,
            actorType: $actorType
        );
    }
}
