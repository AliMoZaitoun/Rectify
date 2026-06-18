<?php

namespace App\DTOs\User\Update;

class UpdateUserDTO
{
    public function __construct(
        public ?string $firstName = null,
        public ?string $lastName = null,
        public ?string $email = null,
        public ?string $phone = null,
        public ?string $location_id = null,
    ) {}

    public static function fromRequest(array $request): self
    {
        return new self(
            firstName: $request['first_name'] ?? null,
            lastName: $request['last_name'] ?? null,
            location_id: $request['location_id'] ?? null,
            phone: $request['phone'] ?? null,
            email: $request['email'] ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'first_name'    => $this->firstName,
            'last_name'     => $this->lastName,
            'email'         => $this->email,
            'phone'         => $this->phone,
            'location_id'   => $this->location_id,
        ], fn($value) => !is_null($value));
    }
}
