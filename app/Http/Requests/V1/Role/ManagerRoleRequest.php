<?php

namespace App\Http\Requests\V1\Role;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ManagerRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id',
        ];
    }
}
