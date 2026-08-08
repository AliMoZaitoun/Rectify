<?php

namespace App\Http\Requests\V1\Complaint;

use App\DTOs\Complaint\ReopenComplaintDTO;
use Illuminate\Foundation\Http\FormRequest;

class ReopenComplaintRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'reason' => ['required', 'string', 'min:5', 'max:1000'],
        ];
    }

    public function toDTO(int $complaintId, ?int $actorId = null, ?string $actorType = null): ReopenComplaintDTO
    {
        return ReopenComplaintDTO::fromRequest($this, $complaintId, $actorId, $actorType);
    }
}
