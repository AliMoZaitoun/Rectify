<?php

namespace App\Http\Requests\V1\Role;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class AssignRoleToUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'roles' => 'required|array',
            'roles.*'  => 'integer|exists:roles,id'
        ];
    }
}
