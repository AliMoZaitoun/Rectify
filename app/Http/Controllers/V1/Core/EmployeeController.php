<?php

namespace App\Http\Controllers\V1\Core;

use App\DTOs\Core\Create\CreateEmployeeDTO;
use App\DTOs\Core\Update\UpdateEmployeeDTO;
use App\DTOs\User\Update\UpdateUserDTO;
use App\DTOs\User\Create\CreateUserDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\EmployeeRequest;
use App\Services\Core\EmployeeService;
use App\Traits\ProvidesUserResource;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    use ResponseTrait, ProvidesUserResource;

    public function __construct(
        private EmployeeService $employeeService
    ) {}

    public function index()
    {
        $employees = $this->employeeService->index();
        return $this->successUserCollection($employees);
    }

    public function store(EmployeeRequest $employeeRequest)
    {
        $userDTO = CreateUserDTO::fromRequest($employeeRequest->validated(), 'employee');

        $employeeDTO = CreateEmployeeDTO::fromRequest($employeeRequest->validated());

        $user = $this->employeeService->store($userDTO, $employeeDTO);
        $user = $this->resolveUserResource($user);
        return $this->successResponse($user, __('messages.common.stored'));
    }

    public function show(int $id)
    {
        $employee = $this->employeeService->show($id);
        $user = $this->resolveUserResource($employee->user);
        return $this->successResponse($user);
    }


    public function update(int $id, Request $request)
    {
        $userDTO = UpdateUserDTO::fromRequest($request->all());

        $employeeDTO = UpdateEmployeeDTO::fromRequest($request->all());

        $user = $this->employeeService->update($id, $userDTO, $employeeDTO);
        $data['user'] = $this->resolveUserResource($user);
        return $this->successResponse($data, __('messages.common.updated'));
    }

    public function destroy(int $id)
    {
        $this->employeeService->destroy($id);
        return $this->successResponse([], __('messages.common.deleted'));
    }
}
