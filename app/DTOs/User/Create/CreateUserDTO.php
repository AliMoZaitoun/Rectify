<?php

namespace App\DTOs\User\Create;

use Illuminate\Support\Facades\Hash;

class CreateUserDTO
{
    public function __construct(
        public ?int $id,
        public string $firstName,
        public string $lastName,
        public string $phone,
        public string $email,
        public string $gender,
        public string $type,
        public string $password,
    ) {}

    public static function fromRequest(array $request, string $type): self
    {
        return new self(
            id: null,
            firstName: $request['first_name'],
            lastName: $request['last_name'],
            phone: $request['phone'],
            email: $request['email'],
            gender: $request['gender'],
            type: $type,
            password: $request['password'],
        );
    }

    public function toArray(): array
    {
        return [
            'first_name' => $this->firstName,
            'last_name'  => $this->lastName,
            'email'      => $this->email,
            'phone'      => $this->phone,
            'gender'     => $this->gender,
            'type'       => $this->type,
            'password'   => Hash::make($this->password),
        ];
    }
}
