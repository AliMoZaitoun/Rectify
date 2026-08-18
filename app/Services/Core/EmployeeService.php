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
        $user = auth()->user();
        $filters = [];

        if ($user && !in_array($user->type, ['admin', 'super_admin'])) {
            $user->loadMissing('employee.currentBranch');
            $employee = $user->employee;

            if ($employee && $employee->currentBranch) {
                $filters['branch_id'] = $employee->currentBranch->branch_id;
            } else {
                $filters['branch_id'] = -1;
            }
        }

        return $this->employeeDAO->index($filters);
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
        return $employee;
    }

    public function update(int $id, UpdateUserDTO $userDTO)
    {
        return $this->transaction->execute(function () use ($id, $userDTO) {
            $employee = $this->show($id);
            $this->userDAO->update($employee->user->id, $userDTO);
            $employee->refresh();
            return $employee;
        });
    }

    public function destroy(int $id)
    {
        return $this->employeeDAO->destroy($id);
    }
}
