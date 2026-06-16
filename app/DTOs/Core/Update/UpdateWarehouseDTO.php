<?php

namespace App\DTOs\Core\Update;

class UpdateWarehouseDTO
{
    public function __construct(
        public ?string $name = null,
        public ?int $location_id = null,
        public ?string $address = null,
    ) {}

    public static function fromRequest(array $request)
    {
        return new self(
            name: $request['name'] ?? null,
            location_id: $request['location_id'] ?? null,
            address: $request['address'] ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'name'     => $this->name,
            'location_id' => $this->location_id,
            'address' => $this->address,
        ], fn($value) => !is_null($value));
    }
}
