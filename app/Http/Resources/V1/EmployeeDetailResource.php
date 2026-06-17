<?php

namespace App\Http\Resources\V1;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $employee = $this->resource instanceof User
            ? $this->resource->employee
            : $this->resource;

        $user = $this->resource instanceof User
            ? $this->resource
            : $this->resource->user;


        return [
            'account' => new UserResource($user),
            'additional_info' => [
                'employee_id'       => $employee->id,
                'position'          => $employee->position
            ],
        ];
    }
}
