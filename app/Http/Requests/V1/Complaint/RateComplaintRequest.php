<?php

namespace App\Http\Requests\V1\Complaint;

use App\DTOs\Complaint\RateComplaintDTO;
use Illuminate\Foundation\Http\FormRequest;

class RateComplaintRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'rating'  => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function toDTO(int $complaintId, ?int $clientId = null): RateComplaintDTO
    {
        return RateComplaintDTO::fromRequest($this, $complaintId, $clientId);
    }
}
