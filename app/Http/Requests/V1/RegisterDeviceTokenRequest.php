<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class RegisterDeviceTokenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'device_id'   => 'required|string',
            'fcm_token'   => 'required|string',
            'device_type' => 'required|string|in:android,ios,web',
        ];
    }
}
