<?php

namespace App\Http\Resources\V1\Complaint;

use App\Enums\ComplaintStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppComplaintHistoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $newStatusEnum = ComplaintStatus::tryFrom($this->new_status);

        return [
            'id'          => $this->id,
            'status'      => $this->new_status,
            'status_label' => $newStatusEnum ? $newStatusEnum->label() : $this->new_status,
            'comment'     => $this->comment,
            'created_at'  => $this->created_at?->toIso8601String(),
        ];
    }
}
