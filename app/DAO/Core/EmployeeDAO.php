<?php

namespace App\DAO\Core;

use App\DTOs\Core\Create\CreateEmployeeDTO;
use App\DTOs\Core\Update\UpdateEmployeeDTO;
use App\Exceptions\NotFoundException;
use App\Models\Core\Employee;

class EmployeeDAO
{

    public function index()
    {
        return Employee::with(['branches'])->get();
    }

    public function store(CreateEmployeeDTO $employeeDTO)
    {
        $employee = Employee::create($employeeDTO->toArray());
        $employee->user->assignRole('employee');
        return $employee;
    }

    public function show(int $id)
    {
        return Employee::where('id', $id)->with(['branches'])->first();
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
