<?php

namespace App\DAO\Core;

use App\DTOs\Core\Create\AssignEmployeeBranchDTO;
use App\DTOs\Core\Update\UpdateEmployeeBranchDTO;
use App\Exceptions\NotFoundException;
use App\Models\Core\EmployeeBranch;

class EmployeeBranchDAO
{

    public function index()
    {
        return EmployeeBranch::with(['employee', 'branch'])->get();
    }

    public function store(AssignEmployeeBranchDTO $dto)
    {
        return EmployeeBranch::create($dto->toArray());
    }

    public function show(int $id)
    {
        return EmployeeBranch::with(['employee', 'branch'])->find($id);
    }

    public function findByEmployee(int $employeeId)
    {
        return EmployeeBranch::with('branch')
            ->where('employee_id', $employeeId)
            ->get();
    }

    public function findByBranch(int $branchId)
    {
        return EmployeeBranch::with('employee')
            ->where('branch_id', $branchId)
            ->get();
    }

    public function update(int $id, UpdateEmployeeBranchDTO $dto)
    {
        $record = $this->show($id);
        $record->update($dto->toArray());
        return $record;
    }

    public function destroy(int $id)
    {
        $record = $this->show($id);
        return $record->delete();
    }

    public function destroyByEmployee(int $employeeId)
    {
        return EmployeeBranch::where('employee_id', $employeeId)->delete();
    }

    public function destroyByBranch(int $branchId)
    {
        return EmployeeBranch::where('branch_id', $branchId)->delete();
    }
}
