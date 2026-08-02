<?php

namespace App\Http\Resources\V1\Compensation;

use App\Http\Resources\V1\ClientDetailResource;
use App\Http\Resources\V1\Complaint\DashboardComplaintResource;
use App\Http\Resources\V1\EmployeeDetailResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DashboardCompensationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'complaint'      => new DashboardComplaintResource($this->whenLoaded('complaint')),
            'client'         => new ClientDetailResource($this->whenLoaded('client')),
            'type'           => $this->type?->value,
            'type_label'     => $this->type?->label(),
            'amount'         => (float) $this->amount,
            'coupon_code'    => $this->coupon_code,
            'notes'          => $this->notes,
            'status'         => $this->status?->value,
            'status_label'   => $this->status?->label(),
            'granted_at'     => $this->granted_at?->toIso8601String(),
            'approved_by'    => new EmployeeDetailResource($this->whenLoaded('approvedBy')),
            'created_at'     => $this->created_at?->toIso8601String(),
        ];
    }
}
