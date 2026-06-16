<?php

namespace App\Services\Core;

use App\DAO\Core\EmployeeDAO;
use App\DAO\Core\EmployeeDepartmentDAO;
use App\DAO\RoleDAO;
use App\DTOs\Core\Create\AssignEmployeeDepartmentDTO;
use App\DTOs\Core\Update\UpdateEmployeeDepartmentDTO;
use App\Services\TransactionService;

class EmployeeDepartmentService
{
    public function __construct(
        private EmployeeDepartmentDAO $employeeDepartmentDAO,
        private TransactionService $transaction,
        private EmployeeDAO $employeeDAO,
        private RoleDAO $roleDAO
    ) {}

    public function index()
    {
        return $this->employeeDepartmentDAO->index();
    }

    public function store(AssignEmployeeDepartmentDTO $dto)
    {
        return $this->transaction->execute(function () use ($dto) {
            $record = $this->employeeDepartmentDAO->store($dto);

            $employee = $this->employeeDAO->show($dto->employee_id);
            $user = $employee->user;

            $this->roleDAO->syncUserRoles($user, ['employee', $this->roleDAO->show($dto->role_id, 'web')]);

            return $record;
        });
    }

    public function show(int $id)
    {
        return $this->employeeDepartmentDAO->show($id);
    }

    public function findByEmployee(int $employeeId)
    {
        return $this->employeeDepartmentDAO->findByEmployee($employeeId);
    }

    public function findByDepartment(int $departmentId)
    {
        return $this->employeeDepartmentDAO->findByDepartment($departmentId);
    }

    public function update(int $id, UpdateEmployeeDepartmentDTO $dto)
    {
        return $this->employeeDepartmentDAO->update($id, $dto);
    }

    public function destroy(int $id)
    {
        return $this->employeeDepartmentDAO->destroy($id);
    }

    public function destroyByEmployee(int $employeeId)
    {
        return $this->employeeDepartmentDAO->destroyByEmployee($employeeId);
    }

    public function destroyByDepartment(int $departmentId)
    {
        return $this->employeeDepartmentDAO->destroyByDepartment($departmentId);
    }
}
