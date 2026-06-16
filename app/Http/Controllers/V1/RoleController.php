<?php

namespace App\Http\Controllers\V1;

use App\DTOs\Role\Create\CreateRoleDTO;
use App\DTOs\Role\Update\UpdateRoleDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\CreateRoleRequest;
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
        return $this->successResponse($roles);
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
        return $this->successResponse($role);
    }

    public function showByName(string $role_name)
    {
        $role = $this->roleService->showByName($role_name);
        return $this->successResponse($role);
    }

    public function update(int $id, Request $request)
    {
        $roleDTO = UpdateRoleDTO::fromRequest($request->all());
        $role = $this->roleService->update($id, $roleDTO);
        return $this->successResponse($role, __('messages.common.updated'));
    }

    public function selectPermission(int $id, Request $request)
    {
        $role = $this->roleService->selectPermission($id, $request->input('permissions'));
        return $this->successResponse($role);
    }

    public function removePermission(int $id, Request $request)
    {
        $role = $this->roleService->removePermission($id, $request->input('permissions'));
        return $this->successResponse($role);
    }

    public function destroy(int $id)
    {
        $this->roleService->destroy($id);
        return $this->successResponse([], __('messages.common.deleted'));
    }
}
