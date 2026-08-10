<?php

namespace App\Services;

use App\DAO\RoleDAO;
use App\DTOs\Role\Create\CreateRoleDTO;
use App\DTOs\Role\Update\UpdateRoleDTO;
use App\Exceptions\V1\PermissionsNeverChangeException;
use Exception;

class RoleService
{
    public function __construct(
        private RoleDAO $roleDAO,
        private Transaction $transaction
    ) {}

    public function index()
    {
        return $this->roleDAO->index();
    }

    public function store(CreateRoleDTO $dto)
    {
        $permissionIds = array_map('intval', $dto->permissions);
        return $this->roleDAO->store($dto, $permissionIds);
    }

    public function show(int $id)
    {
        return $this->roleDAO->show($id);
    }

    public function showByName(string $role_name)
    {
        return $this->roleDAO->showByName($role_name);
    }

    public function update(int $id, UpdateRoleDTO $dto)
    {
        $permissionIds = [];
        if ($dto->permissions)
            $permissionIds = array_map('intval', $dto->permissions);
        $role = $this->roleDAO->update($id, $dto, $permissionIds);
        return $role;
    }

    public function assignUserRoles(int $user_id, array $roles)
    {
        $roleIds = array_map('intval', $roles);

        return $this->roleDAO->assignUserRoles($user_id, $roleIds);
    }

    public function selectPermission(int $id, array $permissions)
    {
        if ($this->show($id)->name == 'admin') {
            throw new PermissionsNeverChangeException();
        }

        $permissionIds = array_map('intval', $permissions);
        return $this->roleDAO->selectPermissions($id, $permissionIds);
    }

    public function destroy(int $id)
    {
        return $this->roleDAO->destroy($id);
    }
}
