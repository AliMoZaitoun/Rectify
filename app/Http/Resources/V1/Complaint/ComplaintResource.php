<?php

namespace App\Http\Resources\V1\Complaint;

use App\Enums\ComplaintPriority;
use App\Enums\ComplaintStatus;
use App\Http\Resources\V1\ClientDetailResource;
use App\Http\Resources\V1\Core\BranchResource;
use App\Http\Resources\V1\EmployeeDetailResource;
use App\Http\Resources\V1\MediaResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ComplaintResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $statusValue = $this->status instanceof ComplaintStatus ? $this->status->value : $this->status;
        $statusLabel = $this->status instanceof ComplaintStatus && method_exists($this->status, 'label')
            ? $this->status->label()
            : $statusValue;

        $priorityValue = $this->priority instanceof ComplaintPriority ? $this->priority->value : $this->priority;
        $priorityLabel = $this->priority instanceof ComplaintPriority && method_exists($this->priority, 'label')
            ? $this->priority->label()
            : $priorityValue;

        return [
            'id'             => $this->id,
            'tracking_code'  => $this->tracking_code,
            'tracking_token' => $this->tracking_token,
            'tracking_url'   => url("/api/v1/complaint/track/{$this->tracking_token}"),
            'is_anonymous'   => (bool) $this->is_anonymous,
            'title'          => $this->title,
            'description'    => $this->description,

            'status'         => $statusValue,
            'status_label'   => $statusLabel,

            'priority'       => $priorityValue,
            'priority_label' => $priorityLabel,

            'sla_due_at'     => $this->sla_due_at?->toIso8601String(),
            'resolved_at'    => $this->resolved_at?->toIso8601String(),
            'created_at'     => $this->created_at?->toIso8601String(),

            'client'      => $this->when($this->whenLoaded('client') && $this->client, function () {
                return new ClientDetailResource($this->client);
            }),

            'branch'      => new BranchResource($this->whenLoaded('branch')),

            'category'    => new CategoryResource($this->whenLoaded('category')),

            'assigned_to' => new EmployeeDetailResource($this->whenLoaded('assignedTo')),

            'attachments' => MediaResource::collection($this->whenLoaded('media')),

            'histories'   => ComplaintHistoryResource::collection($this->whenLoaded('histories')),

            'actions'     => ComplaintActionResource::collection($this->whenLoaded('actions')),

            'ai' => [
                'summary'            => $this->ai_summary,
                'suggested_category' => $this->ai_suggested_category,
                'is_spam'             => (bool) $this->is_spam,
            ],
        ];
    }
}
