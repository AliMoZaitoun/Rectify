<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'full_name'     => $this->fullName,
            'email'         => $this->email,
            'phone'         => $this->phone,
            'type'          => $this->type,
            'address'       => $this->address,
            'created_at'    => $this->created_at->format('Y-m-d h:i A'),
            'created_from'  => $this->created_at->diffForHumans(),
            'verified_at'   => $this?->email_verified_at?->format('Y-m-d h:i A'),
            'roles'         => $this->getRoleNames()
        ];
    }
}
