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
            'id'               => $client->id,
            'account'          => new UserResource($user),
            'additional_info'  => [
                'client_id' => $client->id,
                'points'    => $client->points,
            ],
            'complaints_count' => $client->complaints_count ?? $client->complaints()->count(),
            'complaints'       => $client->relationLoaded('complaints') ? $client->complaints : null,
            'compensations'    => $client->relationLoaded('compensations') ? $client->compensations : null,
        ];
    }
}
