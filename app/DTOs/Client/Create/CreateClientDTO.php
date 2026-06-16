<?php

namespace App\DTOs\Client\Create;

use Illuminate\Support\Facades\Date;

class CreateClientDTO
{
    public function __construct(
        public ?int $id,
        public ?int $user_id,
        public ?int $points,
    ) {}

    public static function fromRequest(array $request): self
    {
        return new self(
            id: null,
            user_id: null,
            points: $request['points'] ?? null
        );
    }

    public function toArray()
    {
        return [
            'user_id' => $this->user_id,
            'points' => $this->points
        ];
    }
}
