<?php

namespace App\Services\Core;

use App\DAO\Core\EmployeeDAO;
use App\DAO\Core\EmployeeBranchDAO;
use App\DTOs\Core\Create\AssignEmployeeBranchDTO;
use App\DTOs\Core\Update\UpdateEmployeeBranchDTO;
use App\Exceptions\NotFoundException;
use App\Exceptions\V1\Core\BranchAlreadyHasManagerException;
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
        if ($dto->position === 'manager') {
            $existingManager = $this->employeeBranchDAO->hasActiveManager($dto->branch_id);

            if ($existingManager) {
                throw new BranchAlreadyHasManagerException();
            }
        }
        return $this->transaction->execute(function () use ($dto) {
            $record = $this->employeeBranchDAO->store($dto);
            $this->employeeDAO->show($dto->employee_id);

            return $record;
        });
    }

    public function show(int $id)
    {
        return $this->employeeBranchDAO->show($id) ?? throw new NotFoundException("Employee-Branch");
    }

    public function byBranch(int $branchId)
    {
        return $this->employeeBranchDAO->byBranch($branchId);
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
