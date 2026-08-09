<?php

namespace App\DAO;

use App\DTOs\Role\Create\CreateRoleDTO;
use App\DTOs\Role\Update\UpdateRoleDTO;
use App\Exceptions\NotFoundException;
use App\Models\User;
use Spatie\Permission\Models\Role;

class RoleDAO
{
    public function __construct(
        private UserDAO $userDAO
    ) {}

    public function index()
    {
        return Role::all();
    }

    public function store(CreateRoleDTO $roleDTO)
    {
        return Role::create($roleDTO->toArray());
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

    public function update(int $id, UpdateRoleDTO $roleDTO)
    {
        $role = $this->show($id);
        return $role->update($roleDTO->toArray());
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
