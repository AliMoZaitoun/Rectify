<?php

namespace App\Http\Requests\V1\Complaint;

use Illuminate\Foundation\Http\FormRequest;

class MergeComplaintsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'child_ids'   => ['required', 'array', 'min:1'],
            'child_ids.*' => [
                'required',
                'integer',
                'exists:complaints,id',
                'different:id'
            ],
        ];
    }
}
