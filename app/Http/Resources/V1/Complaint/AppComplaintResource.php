<?php

namespace App\Http\Resources\V1\Complaint;

use App\Enums\ComplaintPriority;
use App\Enums\ComplaintStatus;
use App\Http\Resources\V1\Compensation\AppCompensationResource;
use App\Http\Resources\V1\Core\BranchResource;
use App\Http\Resources\V1\MediaResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppComplaintResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $statusValue = $this->status instanceof ComplaintStatus ? $this->status->value : $this->status;
        $statusLabel = $this->status instanceof ComplaintStatus && method_exists($this->status, 'label')
            ? $this->status->label()
            : $statusValue;

        return [
            'tracking_code'  => $this->tracking_code,
            'is_anonymous'   => (bool) $this->is_anonymous,
            'title'          => $this->title,
            'description'    => $this->description,

            'status'         => $statusValue,
            'status_label'   => $statusLabel,

            'created_at'     => $this->created_at?->toIso8601String(),
            'resolved_at'    => $this->resolved_at?->toIso8601String(),

            'branch'         => new BranchResource($this->whenLoaded('branch')),
            'category'       => new CategoryResource($this->whenLoaded('category')),
            'attachments'    => MediaResource::collection($this->whenLoaded('media')),
            'actions'        => ComplaintActionResource::collection($this->whenLoaded('actions')),
            'histories'      => AppComplaintHistoryResource::collection($this->whenLoaded('histories')),

            'compensation' => new AppCompensationResource($this->whenLoaded('compensation')),
        ];
    }
}
