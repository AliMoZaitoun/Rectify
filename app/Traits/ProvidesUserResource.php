<?php

namespace App\Traits;

use App\Http\Resources\V1\EmployeeDetailResource;
use App\Http\Resources\V1\ClientDetailResource;
use App\Http\Resources\V1\UserResource;
use App\Models\User;

trait ProvidesUserResource
{
    public function resolveUserResource(User $user)
    {
        $user->loadMissing('roles');

        if ($user->type === 'employee') {
            $user->loadMissing('employee');
        } elseif ($user->type === 'client') {
            $user->loadMissing('client');
        }
        return match ($user->type) {
            'employee' => new EmployeeDetailResource($user),
            'client'   => new ClientDetailResource($user),
            default    => ['account' => new UserResource($user)],
        };
    }
}
