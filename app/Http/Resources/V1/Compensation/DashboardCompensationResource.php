<?php

namespace App\Http\Resources\V1\Compensation;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DashboardCompensationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'complaint_id'   => $this->complaint_id,
            'type'           => $this->type?->value,
            'type_label'     => $this->type?->label(),
            'amount'         => (float) $this->amount,
            'coupon_code'    => $this->coupon_code,
            'notes'          => $this->notes,
            'status'         => $this->status?->value,
            'status_label'   => $this->status?->label(),
            'granted_at'     => $this->granted_at?->toIso8601String(),
            'approved_by'    => $this->whenLoaded('approvedBy', fn() => [
                'id'   => $this->approvedBy->id,
                'name' => $this->approvedBy->name ?? null,
            ]),
            'created_at'     => $this->created_at?->toIso8601String(),
        ];
    }
}
