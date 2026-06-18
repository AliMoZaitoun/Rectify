<?php

namespace App\DAO\Core;

use App\DTOs\Core\Create\CreateBranchDTO;
use App\DTOs\Core\Update\UpdateBranchDTO;
use App\Exceptions\NotFoundException;
use App\Models\Core\Branch;

class BranchDAO
{

    public function index()
    {
        return Branch::with(['employees', 'employees.employee'])->get();
    }

    public function store(array $data)
    {
        return Branch::create($data);
    }

    public function show(int $id)
    {
        return Branch::where('id', $id)->with('employees')->first();
    }

    public function update(int $id, array $data)
    {
        $branch = $this->show($id);
        $branch->update($data);
        return $branch;
    }

    public function destroy(int $id)
    {
        $branch = $this->show($id);
        return $branch->delete();
    }
}
