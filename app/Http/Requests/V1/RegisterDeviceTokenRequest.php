<?php

namespace App\Http\Requests\V1;

use App\Enums\DeviceType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class RegisterDeviceTokenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'fcm_token'   => ['required', 'string'],
            'device_type' => ['required', new Enum(DeviceType::class)],
        ];
    }
}
