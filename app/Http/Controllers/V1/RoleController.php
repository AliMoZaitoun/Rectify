<?php

namespace App\Http\Controllers\V1;

use App\DTOs\Role\Create\CreateRoleDTO;
use App\DTOs\Role\Update\UpdateRoleDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Role\AssignRoleToUserRequest;
use App\Http\Requests\V1\Role\CreateRoleRequest;
use App\Http\Requests\V1\Role\ManagerRoleRequest;
use App\Http\Resources\V1\Core\RoleResource;
use App\Services\RoleService;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    use ResponseTrait;
    public function __construct(
        private RoleService $roleService
    ) {}

    public function index()
    {
        $roles = $this->roleService->index();
        return $this->successCollection($roles, RoleResource::class);
    }

    public function store(CreateRoleRequest $request)
    {
        $roleDTO = CreateRoleDTO::fromRequest($request->validated());

        $role = $this->roleService->store($roleDTO);
        return $this->successResponse($role, __('messages.common.stored'));
    }

    public function show(int $id)
    {
        $role = $this->roleService->show($id);
        return $this->useResource($role, RoleResource::class);
    }

    public function showByName(string $role_name)
    {
        $role = $this->roleService->showByName($role_name);
        return $this->successResponse($role);
    }

    public function assignRoles(int $user_id, AssignRoleToUserRequest $request)
    {
        $this->roleService->assignUserRoles($user_id, $request->input("roles"));
        return $this->successResponse([]);
    }

    public function update(int $id, Request $request)
    {
        $roleDTO = UpdateRoleDTO::fromRequest($request->all());
        $role = $this->roleService->update($id, $roleDTO);
        return $this->successResponse($role, __('messages.common.updated'));
    }

    public function selectPermission(int $role_id, ManagerRoleRequest $request)
    {
        $role = $this->roleService->selectPermission($role_id, $request->validated());
        return $this->successResponse($role);
    }

    public function destroy(int $id)
    {
        $this->roleService->destroy($id);
        return $this->successResponse([], __('messages.common.deleted'));
    }
}
