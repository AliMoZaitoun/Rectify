<?php

namespace App\Http\Resources\V1\Complaint;

use App\Http\Resources\V1\MediaResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ComplaintActionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'action_type'       => $this->action_type,
            'action_type_label' => __("labels.action_types.{$this->action_type}"),
            'content'           => $this->content,
            'created_at'        => $this->created_at?->toIso8601String(),

            'attachments'       => MediaResource::collection($this->whenLoaded('media')),

            'actor' => $this->whenLoaded('actor', function () {
                return [
                    'id'   => $this->actor->id,
                    'name' => $this->actor->full_name ?? ($this->actor->first_name . ' ' . $this->actor->last_name) ?? 'Guest',
                    'type' => class_basename($this->actor_type),
                ];
            }),
        ];
    }
}
