<?php

namespace App\Http\Resources\V1\Complaint;

use App\Http\Resources\V1\EmployeeDetailResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DashboardComplaintHistoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'old_status'        => $this->old_status,
            'new_status'        => $this->new_status,
            'duration_in_hours' => (int) $this->duration_in_hours,
            'comment'           => $this->comment,
            'created_at'        => $this->created_at?->toIso8601String(),

            'assigned_to' => new EmployeeDetailResource($this->whenLoaded('assignedTo')),

            'changed_by'  => $this->whenLoaded('changedBy', function () {
                return [
                    'id'   => $this->changedBy->id,
                    'name' => $this->changedBy->name ?? $this->changedBy->full_name ?? 'System',
                    'type' => class_basename($this->changed_by_type),
                ];
            }),
        ];
    }
}
