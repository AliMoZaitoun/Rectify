<?php

namespace App\Http\Requests\V1\Complaint;

use App\DTOs\Complaint\ComplaintReportFilterDTO;
use Illuminate\Foundation\Http\FormRequest;

class GetComplaintReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'from_date' => ['nullable', 'date', 'before_or_equal:to_date'],
            'to_date'   => ['nullable', 'date', 'after_or_equal:from_date'],
            'branch_id' => ['nullable', 'integer', 'exists:branches,id'],
        ];
    }

    public function toDTO(): ComplaintReportFilterDTO
    {
        return ComplaintReportFilterDTO::fromRequest($this);
    }
}
