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
        return EmployeeBranch::where('id', $id)->with(['employee', 'branch'])->first();
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
}
