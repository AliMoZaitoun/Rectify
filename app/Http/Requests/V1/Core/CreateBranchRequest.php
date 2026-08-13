<?php

namespace App\Http\Requests\V1\Core;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateBranchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'                  => 'required|string',
            'description'           => 'nullable|string',
            'monthly_amount_budget' => ['nullable', 'numeric', 'min:0'],
            'monthly_points_budget' => ['nullable', 'integer', 'min:0'],
            'location_id'           => 'required|exists:locations,id'
        ];
    }
}
