<?php

namespace App\Http\Requests\V1\Complaint;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateComplaintActionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'action_type'   => ['nullable', 'string', Rule::in(['comment', 'request_documents', 'document_submitted', 'client_reply'])],
            'content'       => ['required', 'string', 'max:2000'],
            'attachments'   => ['nullable', 'array'],
            'attachments.*' => ['file', 'max:10240', 'mimes:jpg,jpeg,png,pdf,docx'],
        ];
    }
}
