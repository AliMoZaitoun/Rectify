<?php

namespace App\Http\Resources\V1;

use App\Http\Resources\V1\MediaResource;
use App\Http\Resources\V1\Sales\ComplaintTypeResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ComplaintResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'type'          => new ComplaintTypeResource($this->whenLoaded('type')),
            'title'         => $this->title,
            'body'          => $this->body,
            'status'        => $this->status,
            'attachments'   => MediaResource::collection($this->whenLoaded('attachments')),

            'created_at'     => $this->created_at->format('Y-m-d h:i A'),
        ];
    }
}
