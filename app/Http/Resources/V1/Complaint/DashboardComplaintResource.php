<?php

namespace App\Http\Resources\V1\Complaint;

use App\Enums\ComplaintPriority;
use App\Enums\ComplaintStatus;
use App\Http\Resources\V1\ClientDetailResource;
use App\Http\Resources\V1\Core\BranchResource;
use App\Http\Resources\V1\EmployeeDetailResource;
use App\Http\Resources\V1\MediaResource;
use App\Http\Resources\V1\Compensation\DashboardCompensationResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DashboardComplaintResource extends JsonResource
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

        $hasRatingForCurrentResolution = $this->relationLoaded('latestRating')
            && $this->latestRating
            && $this->resolved_at
            && $this->latestRating->created_at->gt($this->resolved_at);

        $isResolved = $statusValue === ComplaintStatus::RESOLVED->value;


        return [
            'id'             => $this->id,
            'tracking_code'  => $this->tracking_code,
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

            'client' => $this->when(!$this->is_anonymous && $this->relationLoaded('client') && $this->client, function () {
                return new ClientDetailResource($this->client);
            }),

            'branch'      => new BranchResource($this->whenLoaded('branch')),
            'category'    => new CategoryResource($this->whenLoaded('category')),
            'assigned_to' => new EmployeeDetailResource($this->whenLoaded('assignedTo')),
            'attachments' => MediaResource::collection($this->whenLoaded('media')),
            'histories'   => DashboardComplaintHistoryResource::collection($this->whenLoaded('histories')),
            'actions'     => ComplaintActionResource::collection($this->whenLoaded('actions')),

            'ai' => [
                'summary'            => $this->ai_summary,
                'suggested_category' => $this->ai_suggested_category,
                'is_spam'             => (bool) $this->is_spam,
            ],

            'can_be_rated'    => $isResolved && ! $hasRatingForCurrentResolution,
            'can_be_reopened' => $isResolved,

            'latest_rating'   => new ComplaintRatingResource($this->whenLoaded('latestRating')),


            'compensation' => new DashboardCompensationResource($this->whenLoaded('compensation'))
        ];
    }
}
