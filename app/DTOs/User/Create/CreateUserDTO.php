<?php

namespace App\DTOs\User\Create;

use Illuminate\Support\Facades\Hash;

class CreateUserDTO
{
    public function __construct(
        public string $firstName,
        public string $lastName,
        public string $location_id,
        public string $phone,
        public string $email,
        public string $gender,
        public string $type,
        public string $password,
    ) {}

    public static function fromRequest(array $request, string $type): self
    {
        return new self(
            firstName: $request['first_name'],
            lastName: $request['last_name'],
            location_id: $request['location_id'],
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
            'first_name'    => $this->firstName,
            'last_name'     => $this->lastName,
            'email'         => $this->email,
            'location_id'   => $this->location_id,
            'phone'         => $this->phone,
            'gender'        => $this->gender,
            'type'          => $this->type,
            'password'      => Hash::make($this->password),
        ];
    }
}
