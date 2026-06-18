<?php

namespace App\Services\Core;

use App\DAO\Core\EmployeeDAO;
use App\DAO\Core\EmployeeBranchDAO;
use App\DTOs\Core\Create\AssignEmployeeBranchDTO;
use App\DTOs\Core\Update\UpdateEmployeeBranchDTO;
use App\Exceptions\NotFoundException;
use App\Services\TransactionService;

class EmployeeBranchService
{
    public function __construct(
        private EmployeeBranchDAO $employeeBranchDAO,
        private TransactionService $transaction,
        private EmployeeDAO $employeeDAO
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

            return $record;
        });
    }

    public function show(int $id)
    {
        return $this->employeeBranchDAO->show($id) ?? throw new NotFoundException("Employee-Branch");
    }


    public function update(int $id, UpdateEmployeeBranchDTO $dto)
    {
        return $this->employeeBranchDAO->update($id, $dto);
    }

    public function destroy(int $id)
    {
        $this->show($id);
        return $this->employeeBranchDAO->destroy($id);
    }
}
