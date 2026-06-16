<?php

namespace App\Http\Resources\V1\Core;

use App\Http\Resources\V1\RealEstate\LocationResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WarehouseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'location'      => new LocationResource($this->location),
            'address'       => $this->address,
            'description'   => $this->description,
            'items'         => ItemResource::collection($this->whenLoaded('items'))
        ];
    }
}
