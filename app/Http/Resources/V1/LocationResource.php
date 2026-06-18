<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LocationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();

        return [
            'id'         => $this->id,
            'name'       => $this->name[$locale] ?? $this->name,
            'type'       => $this->type,
            'parent_id'  => $this->parent_id,
            'parent'     => new LocationResource($this->whenLoaded('parent')),
            'children'   => LocationResource::collection($this->whenLoaded('children')),
            'created_at'     => $this->created_at->format('Y-m-d h:i A'),
        ];
    }
}
