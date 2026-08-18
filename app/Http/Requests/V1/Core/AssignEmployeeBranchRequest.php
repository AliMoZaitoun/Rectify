<?php

namespace App\Http\Requests\V1\Core;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class AssignEmployeeBranchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'employee_id' => 'required|integer|exists:employees,id',
            'branch_id' => 'required|integer|exists:branches,id',
            'position' => 'required|string|in:manager,staff',
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date',
        ];
    }
}
