<?php

namespace App\Http\Resources\V1\Core;

use App\Http\Resources\V1\EmployeeDetailResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeDepartmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'employee'   => new EmployeeDetailResource($this->whenLoaded('employee')),
            'department' => new DepartmentResource($this->whenLoaded('department')),
            'position'   => $this->position,
            'from_date'  => $this->from_date,
            'to_date'    => $this->to_date,
        ];
    }
}
