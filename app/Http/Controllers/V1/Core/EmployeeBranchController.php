<?php

namespace App\Http\Controllers\V1\Core;

use App\DTOs\Core\Create\AssignEmployeeBranchDTO;
use App\DTOs\Core\Update\UpdateEmployeeBranchDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Core\AssignEmployeeBranchRequest;
use App\Http\Resources\V1\Core\EmployeeBranchResource;
use App\Services\Core\EmployeeBranchService;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class EmployeeBranchController extends Controller
{
    use ResponseTrait;
    public function __construct(
        private EmployeeBranchService $employeeBranchService
    ) {}

    public function index()
    {
        $emp_deps = $this->employeeBranchService->index();
        return $this->successCollection($emp_deps, EmployeeBranchResource::class);
    }

    public function store(AssignEmployeeBranchRequest $request)
    {
        $dto = AssignEmployeeBranchDTO::fromRequest($request->validated());

        $emp_dep = $this->employeeBranchService->store($dto);
        return $this->useResource($emp_dep, EmployeeBranchResource::class, __('messages.common.stored'), 201);
    }

    public function show(int $id)
    {
        $emp_dep = $this->employeeBranchService->show($id);
        return $this->useResource($emp_dep, EmployeeBranchResource::class);
    }

    public function findByEmployee(int $employeeId)
    {
        $Branchs = $this->employeeBranchService->findByEmployee($employeeId);
        return $this->successCollection($Branchs, EmployeeBranchResource::class);
    }

    public function findByBranch(int $BranchId)
    {
        $employees = $this->employeeBranchService->findByBranch($BranchId);
        return $this->successCollection($employees, EmployeeBranchResource::class);
    }

    public function update(int $id, Request $request)
    {
        $dto = UpdateEmployeeBranchDTO::fromRequest($request->all());
        $emp_data = $this->employeeBranchService->update($id, $dto);
        return $this->useResource($emp_data, EmployeeBranchResource::class, __('messages.common.updated'));
    }

    public function destroy(int $id)
    {
        $this->employeeBranchService->destroy($id);
        return $this->successResponse([], __('messages.common.deleted'));
    }

    public function destroyByEmployee(int $employeeId)
    {
        $this->employeeBranchService->destroyByEmployee($employeeId);
        return $this->successResponse([], __('messages.common.deleted'));
    }

    public function destroyByBranch(int $BranchId)
    {
        $this->employeeBranchService->destroyByBranch($BranchId);
        return $this->successResponse([], __('messages.common.deleted'));
    }
}
