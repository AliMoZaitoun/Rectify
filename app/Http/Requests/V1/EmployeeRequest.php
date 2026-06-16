<?php

namespace App\Http\Requests\V1;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class EmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return array_merge([
            'employee_id'   => 'nullable|integer|exists:employees,id',
            'branch_id'     => 'nullable|integer|exists:branches,id',
            'position'      => 'nullable|string|in:manager,supervisor,staff',
            'role_id'       => 'nullable|integer|exists:roles,id',
            'from_date'     => 'nullable|date',
        ], (new SignUpRequest())->rules());
    }
}
