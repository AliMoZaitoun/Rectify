<?php

namespace App\DTOs\Role\Create;

class CreateRoleDTO
{
    public function __construct(
        public string $name,
        public ?string $guard_name
    ) {}

    public static function fromRequest(array $request)
    {
        return new self(
            name: $request['name'],
            guard_name: $request['guard_name']
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'name'          => $this->name,
            'guard_name'    => $this->guard_name
        ], fn($value) => !is_null($value));
    }
}
