<?php

namespace App\DTOs\Complaint;

use App\Enums\CompensationType;
use App\Http\Requests\V1\Complaint\StoreCompensationRequest;

class CompensationDTO
{
    public function __construct(
        public ?int $complaintId,
        public ?int $clientId,
        public ?int $approvedById,
        public CompensationType $type,
        public float $amount,
        public ?string $couponCode,
        public ?string $notes,
        public ?bool $applyToChildren = false
    ) {}

    public static function fromRequest(
        StoreCompensationRequest $request,
        ?int $complaintId,
        ?int $clientId,
        ?int $employeeId
    ): self {
        return new self(
            complaintId: $complaintId,
            clientId: $clientId,
            approvedById: $employeeId,
            type: CompensationType::from($request->validated('type')),
            amount: (float) $request->validated('amount', 0),
            couponCode: $request->validated('coupon_code'),
            notes: $request->validated('notes'),
            applyToChildren: $request->boolean('apply_to_children')
        );
    }
}
