<?php

namespace App\Http\Resources\V1\Core;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'sku'           => $this->sku,
            'name'          => $this->name,
            'description'   => $this->description,
            'quantity'      => $this->quantity,
            'status'        => $this->status,
            'expiry_date'   => $this->expiry_date,
            'purchase_date' => $this->purchase_date,
            'received_date' => $this->received_date,

            'warehouse'     => new WarehouseResource($this->whenLoaded('warehouse'))
        ];
    }
}
