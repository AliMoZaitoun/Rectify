<?php

namespace App\DTOs\Role\Create;

class CreateRoleDTO
{
    public function __construct(
        public string $name,
        public ?string $guard_name,
        public array $permissions
    ) {}

    public static function fromRequest(array $request)
    {
        return new self(
            name: $request['name'],
            guard_name: $request['guard_name'] ?? null,
            permissions: $request['permissions'] ?? []
        );
    }

    public function toArray(): array
    {
        return [
            'name'       => $this->name,
            'guard_name' => $this->guard_name,
        ];
    }
}
