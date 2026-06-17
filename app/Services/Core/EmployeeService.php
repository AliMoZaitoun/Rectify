<?php

namespace App\Services\Core;

use App\DAO\Core\EmployeeDAO;
use App\DAO\Core\EmployeeBranchDAO;
use App\DAO\UserDAO;
use App\DTOs\Core\Create\AssignEmployeeBranchDTO;
use App\DTOs\Core\Create\CreateEmployeeDTO;
use App\DTOs\Core\Update\UpdateEmployeeDTO;
use App\DTOs\User\Update\UpdateUserDTO;
use App\DTOs\User\Create\CreateUserDTO;
use App\Exceptions\NotFoundException;
use App\Services\Core\EmployeeBranchService;
use App\Services\Transaction;

class EmployeeService
{
    public function __construct(
        private EmployeeDAO $employeeDAO,
        private UserDAO $userDAO,
        private EmployeeBranchDAO $employeeBranchDAO,
        private EmployeeBranchService $employeeBranchService,
        private Transaction $transaction
    ) {}

    public function index()
    {
        $employees = $this->employeeDAO->index();
        if (sizeof($employees) <= 0)
            throw new NotFoundException("Employees");
        return $employees;
    }

    public function store(CreateUserDTO $userDTO, CreateEmployeeDTO $employeeDTO)
    {
        return $this->transaction->execute(function () use ($userDTO, $employeeDTO) {
            $user = $this->userDAO->store($userDTO);
            $this->userDAO->verify($user);

            $employeeDTO->user_id = $user->id;
            $employee = $this->employeeDAO->store($employeeDTO);

            $this->assignBranchIfProvided($employee->id, $employeeDTO);

            return $user;
        });
    }

    # Helper Function
    private function assignBranchIfProvided(int $employeeId, CreateEmployeeDTO $dto): void
    {
        if (!$dto->branch_id || !$dto->position) {
            return;
        }

        $assignDTO = AssignEmployeeBranchDTO::fromRequest(array_merge(
            $dto->getDepInfo(),
            ['employee_id' => $employeeId]
        ));

        $this->employeeBranchService->store($assignDTO);
    }

    public function show(int $id)
    {
        $employee = $this->employeeDAO->show($id);
        return $employee->user;
    }

    public function update(int $id, UpdateUserDTO $userDTO, UpdateEmployeeDTO $employeeDTO)
    {
        return $this->transaction->execute(function () use ($id, $userDTO, $employeeDTO) {
            $employee = $this->show($id);
            $this->userDAO->update($employee->user->id, $userDTO);
            $this->employeeDAO->update($id, $employeeDTO);
            $employee->refresh();
            return $employee;
        });
    }

    public function destroy(int $id)
    {
        $this->employeeBranchDAO->destroyByEmployee($id);
        return $this->employeeDAO->destroy($id);
    }
}
