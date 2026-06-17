<?php

namespace App\Http\Resources\V1;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $client = $this->resource instanceof User
            ? $this->resource->client
            : $this->resource;

        $user = $this->resource instanceof User
            ? $this->resource
            : $this->resource->user;

        return [
            'account' => new UserResource($user),
            'additional_info' => [
                'points'        => $client->points,
            ],
        ];
    }
}
