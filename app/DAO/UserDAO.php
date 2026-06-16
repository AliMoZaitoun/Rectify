<?php

namespace App\DAO;

use App\DTOs\User\Update\UpdateUserDTO;
use App\DTOs\User\Create\CreateUserDTO;
use App\Exceptions\NotFoundException;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserDAO
{
    public function store(CreateUserDTO $userDTO)
    {
        return User::create($userDTO->toArray());
    }

    public function findById(int $id)
    {
        return User::find($id) ?? throw new NotFoundException("User");
    }

    public function findByField(string $type, string $value)
    {
        return User::where($type, $value)->first() ?? throw new NotFoundException("User");
    }

    public function update(int $id, UpdateUserDTO $userDTO)
    {
        $user = $this->findById($id);
        return $user->update($userDTO->toArray());
    }

    public function verify(User $user)
    {
        return $user->update(['email_verified_at' => now()]);
    }

    public function updatePassword(User $user, string $newPassword)
    {
        return $user->update(['password' => Hash::make($newPassword)]);
    }

    public function findByEmail(string $email)
    {
        return User::where('email', $email)->first() ?? throw new NotFoundException("User");
    }

    public function delete(int $id)
    {
        $user = $this->findById($id);
        return $user->delete();
    }
}
