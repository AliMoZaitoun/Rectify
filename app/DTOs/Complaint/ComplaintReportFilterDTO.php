<?php

namespace App\DTOs\Complaint;

use App\Http\Requests\V1\Complaint\GetComplaintReportRequest;

class ComplaintReportFilterDTO
{
    public function __construct(
        public readonly ?string $fromDate = null,
        public readonly ?string $toDate = null,
        public readonly ?int $branchId = null
    ) {}

    public static function fromRequest(GetComplaintReportRequest $request): self
    {
        return new self(
            fromDate: $request->validated('from_date'),
            toDate: $request->validated('to_date'),
            branchId: $request->validated('branch_id') ? (int) $request->validated('branch_id') : null
        );
    }
}
