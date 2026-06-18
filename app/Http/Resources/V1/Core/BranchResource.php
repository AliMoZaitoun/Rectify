<?php

namespace App\Http\Resources\V1\Core;

use App\Http\Resources\V1\EmployeeDetailResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BranchResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'description'   => $this->description,
            'created_at'    => $this->created_at->format('Y-m-d h:i A'),

            'employees'     => EmployeeBranchResource::collection($this->whenLoaded('employees'))
        ];
    }
}
