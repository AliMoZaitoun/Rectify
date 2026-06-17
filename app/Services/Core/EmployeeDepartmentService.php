<?php

namespace App\Services\Core;

use App\DAO\Core\EmployeeDAO;
use App\DAO\Core\EmployeeBranchDAO;
use App\DAO\RoleDAO;
use App\DTOs\Core\Create\AssignEmployeeBranchDTO;
use App\DTOs\Core\Update\UpdateEmployeeBranchDTO;
use App\Services\TransactionService;

class EmployeeBranchService
{
    public function __construct(
        private EmployeeBranchDAO $employeeBranchDAO,
        private TransactionService $transaction,
        private EmployeeDAO $employeeDAO,
        // private RoleDAO $roleDAO
    ) {}

    public function index()
    {
        return $this->employeeBranchDAO->index();
    }

    public function store(AssignEmployeeBranchDTO $dto)
    {
        return $this->transaction->execute(function () use ($dto) {
            $record = $this->employeeBranchDAO->store($dto);

            $employee = $this->employeeDAO->show($dto->employee_id);
            $user = $employee->user;

            // $this->roleDAO->syncUserRoles($user, ['employee', $this->roleDAO->show($dto->role_id, 'web')]);

            return $record;
        });
    }

    public function show(int $id)
    {
        return $this->employeeBranchDAO->show($id);
    }

    public function findByEmployee(int $employeeId)
    {
        return $this->employeeBranchDAO->findByEmployee($employeeId);
    }

    public function findByBranch(int $branchId)
    {
        return $this->employeeBranchDAO->findByBranch($branchId);
    }

    public function update(int $id, UpdateEmployeeBranchDTO $dto)
    {
        return $this->employeeBranchDAO->update($id, $dto);
    }

    public function destroy(int $id)
    {
        return $this->employeeBranchDAO->destroy($id);
    }

    public function destroyByEmployee(int $employeeId)
    {
        return $this->employeeBranchDAO->destroyByEmployee($employeeId);
    }

    public function destroyByBranch(int $branchId)
    {
        return $this->employeeBranchDAO->destroyByBranch($branchId);
    }
}
