<?php

namespace App\Services\Core;

use App\DAO\Core\DepartmentDAO;
use App\DAO\Core\EmployeeDepartmentDAO;
use App\DTOs\Core\Create\AssignEmployeeDepartmentDTO;
use App\DTOs\Core\Create\CreateDepartmentDTO;
use App\DTOs\Core\Update\UpdateDepartmentDTO;
use App\Services\FileManagerService;
use App\Services\Transaction;
use App\Services\TranslationService;

class DepartmentService
{
    public function __construct(
        private DepartmentDAO $departmentDAO,
        private EmployeeDepartmentDAO $employeeDepartmentDAO,
        private TranslationService $translationService,
        private Transaction $transaction
    ) {}

    public function index()
    {
        return $this->departmentDAO->index();
    }

    public function store(CreateDepartmentDTO $dto)
    {
        return $this->transaction->execute(function () use ($dto) {
            $data = $dto->toArray();
            $data['name'] = $this->translationService->translateAll($dto->name);

            if ($dto->description) {
                $data['description'] = $this->translationService->translateAll($dto->description);
            }

            return $this->departmentDAO->store($data);
        });
    }

    public function show(int $id)
    {
        return $this->departmentDAO->show($id);
    }

    public function update(int $id, UpdateDepartmentDTO $departmentDTO)
    {
        return $this->departmentDAO->update($id, $departmentDTO);
    }

    public function destroy(int $id)
    {
        $this->employeeDepartmentDAO->destroyByDepartment($id);
        return $this->departmentDAO->destroy($id);
    }

    public function assign(AssignEmployeeDepartmentDTO $dto)
    {
        return $this->employeeDepartmentDAO->store($dto);
    }
}
