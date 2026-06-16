<?php

namespace App\DAO\Core;

use App\DTOs\Core\Create\AssignEmployeeDepartmentDTO;
use App\DTOs\Core\Update\UpdateEmployeeDepartmentDTO;
use App\Exceptions\NotFoundException;
use App\Models\Core\EmployeeDepartment;

class EmployeeDepartmentDAO
{

    public function index()
    {
        return EmployeeDepartment::with(['employee', 'department'])->get();
    }

    public function store(AssignEmployeeDepartmentDTO $dto)
    {
        return EmployeeDepartment::create($dto->toArray());
    }

    public function show(int $id)
    {
        return EmployeeDepartment::with(['employee', 'department'])->find($id)
            ?? throw new NotFoundException("EmployeeDepartment");
    }

    public function findByEmployee(int $employeeId)
    {
        return EmployeeDepartment::with('department')
            ->where('employee_id', $employeeId)
            ->get();
    }

    public function findByDepartment(int $departmentId)
    {
        return EmployeeDepartment::with('employee')
            ->where('department_id', $departmentId)
            ->get();
    }

    public function update(int $id, UpdateEmployeeDepartmentDTO $dto)
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
        return EmployeeDepartment::where('employee_id', $employeeId)->delete();
    }

    public function destroyByDepartment(int $departmentId)
    {
        return EmployeeDepartment::where('department_id', $departmentId)->delete();
    }
}
