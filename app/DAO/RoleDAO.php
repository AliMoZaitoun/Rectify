<?php

namespace App\DAO;

use App\DTOs\Role\Create\CreateRoleDTO;
use App\DTOs\Role\Update\UpdateRoleDTO;
use App\Exceptions\NotFoundException;
use App\Models\User;
use App\Services\Transaction;
use Spatie\Permission\Models\Role;

class RoleDAO
{
    public function __construct(
        private UserDAO $userDAO,
        private Transaction $transaction
    ) {}

    public function index()
    {
        return Role::all();
    }

    public function store(CreateRoleDTO $dto, array $permissionsIds = [])
    {
        return $this->transaction->execute(function () use ($dto, $permissionsIds) {
            $role = Role::create($dto->toArray());

            if (!empty($permissionsIds)) {
                $role->syncPermissions($permissionsIds);
            }

            return $role;
        });
    }

    public function show(int $id, $guardName = 'web')
    {
        return Role::findById($id, $guardName) ?? throw new NotFoundException('Role');
    }

    public function showByName(string $role_name)
    {
        return Role::findByName($role_name) ?? throw new NotFoundException('Role');
    }

    public function assignUserRoles(int $user_id, array $roles)
    {
        $user = $this->userDAO->findById($user_id);
        return $user->syncRoles($roles);
    }

    public function removeUserRoles(int $user_id, array $roles)
    {
        $user = $this->userDAO->findById($user_id);
        foreach ($roles as $role) {
            $user->removeRole($role);
        }
        return $user;
    }

    public function update(int $id, UpdateRoleDTO $dto, $permissionsIds = [])
    {
        return $this->transaction->execute(function () use ($id, $dto, $permissionsIds) {
            $role = $this->show($id);
            $role->update($dto->toArray());

            if (!empty($permissionsIds)) {
                $role->syncPermissions($permissionsIds);
            }

            return $role;
        });
    }

    public function selectPermissions(int $id, array $permissions)
    {
        $role = $this->show($id);
        return $role->syncPermissions($permissions);
    }

    public function destroy(int $id)
    {
        $role = $this->show($id);
        return $role->delete();
    }
}
