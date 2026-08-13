<?php

namespace App\Http\Requests\V1\AI;

use Illuminate\Foundation\Http\FormRequest;

class GenerateAiReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'           => ['required', 'string', 'max:255'],
            'complaint_ids'   => ['nullable', 'array'],
            'complaint_ids.*' => ['integer', 'exists:complaints,id'],
            'date_from'       => ['nullable', 'date', 'before_or_equal:date_to'],
            'date_to'         => ['nullable', 'date', 'after_or_equal:date_from'],
            'branch_id'       => ['nullable', 'integer', 'exists:branches,id'],
        ];
    }
}
