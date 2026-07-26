<?php

namespace App\Services\Core;

use App\DAO\Core\BranchDAO;
use App\DAO\Core\EmployeeBranchDAO;
use App\DTOs\Core\Create\AssignEmployeeBranchDTO;
use App\DTOs\Core\Create\CreateBranchDTO;
use App\DTOs\Core\Update\UpdateBranchDTO;
use App\Exceptions\NotFoundException;
use App\Services\FileManagerService;
use App\Services\Transaction;
use App\Services\TranslationService;

class BranchService
{
    public function __construct(
        private BranchDAO $branchDAO,
        private EmployeeBranchDAO $employeebranchDAO,
        private TranslationService $translationService,
        private Transaction $transaction
    ) {}

    public function index()
    {
        return $this->branchDAO->index();
    }

    public function store(CreateBranchDTO $dto)
    {
        return $this->transaction->execute(function () use ($dto) {
            $data = $dto->toArray();
            $data['name'] = $this->translationService->translateAll($dto->name);

            if ($dto->description) {
                $data['description'] = $this->translationService->translateAll($dto->description);
            }


            return $this->branchDAO->store($data);
        });
    }

    public function show(int $id)
    {
        $branch = $this->branchDAO->show($id);
        if (!$branch) {
            throw new NotFoundException("Branch");
        }
        return $branch;
    }

    public function update(int $id, UpdateBranchDTO $dto)
    {
        $data = $dto->toArray();
        if ($data['name'])
            $data['name'] = $this->translationService->translateAll($dto->name);

        if ($data['description']) {
            $data['description'] = $this->translationService->translateAll($dto->description);
        }
        return $this->branchDAO->update($id, $data);
    }

    public function destroy(int $id)
    {
        // $this->employeebranchDAO->destroyByBranch($id);
        return $this->branchDAO->destroy($id);
    }

    public function assign(AssignEmployeeBranchDTO $dto)
    {
        return $this->employeebranchDAO->store($dto);
    }
}
