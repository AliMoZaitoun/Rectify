<?php

namespace App\DAO\Core;

use App\DTOs\Core\Create\CreateEmployeeDTO;
use App\DTOs\Core\Update\UpdateEmployeeDTO;
use App\Exceptions\NotFoundException;
use App\Models\Core\Employee;

class EmployeeDAO
{

    public function index(array $filters = [])
    {
        $query = Employee::with(['user', 'currentBranch.branch', 'employeeBranches.branch']);

        if (isset($filters['branch_id'])) {
            if ($filters['branch_id'] === -1) {
                return collect();
            }

            $query->whereHas('currentBranch', function ($q) use ($filters) {
                $q->where('branch_id', $filters['branch_id']);
            });
        }

        return $query->get();
    }

    public function store(CreateEmployeeDTO $employeeDTO)
    {
        $employee = Employee::create($employeeDTO->toArray());
        $employee->user->assignRole('staff');
        return $employee;
    }

    public function show(int $id)
    {
        return Employee::where('id', $id)->with([
            'user',
            'currentBranch.branch',
            'employeeBranches.branch'
        ])->first();
    }

    public function update(int $id, UpdateEmployeeDTO $employeeDTO)
    {
        $employee = $this->show($id);
        $employee->update($employeeDTO->toArray());
        return $employee;
    }

    public function destroy(int $id)
    {
        $employee = $this->show($id);
        return $employee->user->delete();
    }
}
