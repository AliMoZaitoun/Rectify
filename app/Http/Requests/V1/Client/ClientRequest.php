<?php

namespace App\Http\Requests\V1\Client;

use App\Http\Requests\V1\SignUpRequest;
use Illuminate\Foundation\Http\FormRequest;

class ClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return array_merge((new SignUpRequest())->rules(), []);
    }
}
