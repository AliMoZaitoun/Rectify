<?php

namespace App\Http\Requests\V1\Complaint;

use App\Enums\ComplaintPriority;
use App\Enums\ComplaintStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class FilterComplaintRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status'      => ['nullable', 'string', new Enum(ComplaintStatus::class)],
            'priority'    => ['nullable', 'string', new Enum(ComplaintPriority::class)],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'per_page'    => ['nullable', 'integer', 'min:1', 'max:100'],
            'is_spam'     => ['nullable']
        ];
    }

    public function filters(): array
    {
        return $this->only(['status', 'priority', 'category_id', 'is_spam']);
    }
}
