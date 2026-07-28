<?php

namespace App\Http\Requests\V1\Complaint;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreComplaintRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'device_id'     => ['required', 'string'],
            'branch_id'     => ['required', 'integer', 'exists:branches,id'],
            'category_id'   => ['required', 'integer', 'exists:categories,id'],
            'title'         => ['required', 'string', 'max:255'],
            'description'   => ['required', 'string', 'min:10'],
            'is_anonymous'  => ['nullable', 'boolean'],

            'attachments'   => ['nullable', 'array', 'max:5'],
            'attachments.*' => ['file', 'mimes:jpg,jpeg,png,mp4,pdf', 'max:10240'],
        ];
    }
}
