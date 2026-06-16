<?php

namespace App\DTOs\Chat\Create;

class CreateRoomDTO
{
    public function __construct(
        private int $client_id,
        private ?int $employee_id,
        private ?string $status
    ) {}

    public static function fromRequest(int $client_id, ?int $employee_id = null, ?string $status = 'open')
    {
        return new self(
            client_id: $client_id,
            employee_id: $employee_id,
            status: $status
        );
    }

    public function toArray()
    {
        return array_filter([
            'client_id' => $this->client_id,
            'employee_id' => $this->employee_id,
            'status'    => $this->status
        ], fn($value) => !is_null($value));
    }
}
