<?php

namespace App\Http\Requests\V1\Complaint;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $locale = app()->getLocale();

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories', "name->{$locale}"),
            ],
            'sla_hours'   => ['required', 'integer', 'min:1', 'max:720'],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
