<?php

namespace App\Http\Requests\V1\Complaint;

use App\Enums\ComplaintStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ChangeComplaintStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status'         => ['required', Rule::enum(ComplaintStatus::class)],
            'assigned_to_id' => ['nullable', 'integer', 'exists:employees,id'],
            'comment'        => ['nullable', 'string', 'max:1000'],
        ];
    }
}
