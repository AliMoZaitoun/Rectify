<?php

namespace App\DTOs\Complaint;

use App\Enums\ComplaintStatus;
use App\Http\Requests\V1\Complaint\ChangeComplaintStatusRequest;
use App\Models\Core\Employee;

class ChangeComplaintStatusDTO
{
    public function __construct(
        public ComplaintStatus $status,
        public ?int $assignedToId = null,
        public ?string $comment = null,
        public ?string $changedByType = null,
        public ?int $changedById = null,
    ) {}

    public static function fromRequest(ChangeComplaintStatusRequest $request, ?Employee $employee = null): self
    {
        return new self(
            status: ComplaintStatus::from($request->validated('status')),
            assignedToId: $request->validated('assigned_to_id'),
            comment: $request->validated('comment'),
            changedByType: $employee ? get_class($employee) : null,
            changedById: $employee?->id,
        );
    }
}
