<?php

namespace App\Http\Requests\V1\Complaint;

use App\Enums\CompensationType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreCompensationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type'              => ['required', new Enum(CompensationType::class)],
            'amount'            => ['required_if:type,points,coupon', 'numeric', 'min:0'],
            'coupon_code'       => ['nullable', 'string', 'max:50'],
            'notes'             => ['nullable', 'string', 'max:1000'],
            'apply_to_children' => ['nullable', 'boolean'],
        ];
    }
}
