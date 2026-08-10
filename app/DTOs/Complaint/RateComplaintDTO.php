<?php

namespace App\DTOs\Complaint;

use App\Http\Requests\V1\Complaint\RateComplaintRequest;

class RateComplaintDTO
{
    public function __construct(
        public readonly string $complaintCode,
        public readonly int $rating,
        public readonly ?string $comment = null,
        public readonly ?int $clientId = null
    ) {}

    public static function fromRequest(RateComplaintRequest $request, string $complaintCode, ?int $clientId = null): self
    {
        return new self(
            complaintCode: $complaintCode,
            rating: (int) $request->validated('rating'),
            comment: $request->validated('comment'),
            clientId: $clientId
        );
    }

    public function toArray(int $complaintId): array
    {
        return array_filter([
            'complaint_id' => $complaintId,
            'rating'       => $this->rating,
            'comment'      => $this->comment,
            'client_id'    => $this->clientId,
        ], fn($value) => ! is_null($value));
    }
}
