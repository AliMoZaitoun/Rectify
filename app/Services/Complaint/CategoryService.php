<?php

namespace App\Services\Complaint;

use App\DAO\Complaint\CategoryDAO;
use App\DTOs\Complaint\Create\CategoryDTO;
use App\Exceptions\NotFoundException;
use App\Models\Complaint\Category;
use App\Services\TransactionService;
use App\Services\TranslationService;
use Illuminate\Database\Eloquent\Collection;

class CategoryService
{
    public function __construct(
        protected CategoryDAO $categoryDAO,
        protected TransactionService $transaction,
        protected TranslationService $translation
    ) {}

    public function getAll(): Collection
    {
        return $this->categoryDAO->all();
    }

    public function findById(int $id): Category
    {
        $category = $this->categoryDAO->byId($id);
        if (!$category) {
            throw new NotFoundException("Category");
        }
        return $category;
    }

    public function createCategory(CategoryDTO $dto): Category
    {
        return $this->transaction->execute(function () use ($dto) {
            $data = $dto->toArray();
            $data['name'] = $this->translation->translateAll($dto->name);

            if ($dto->description) {
                $data['description'] = $this->translation->translateAll($dto->description);
            }

            return $this->categoryDAO->store($data);
        });
    }

    public function updateCategory(int $id, CategoryDTO $dto): Category
    {
        return $this->transaction->execute(function () use ($id, $dto) {
            $category = $this->findById($id);
            $data = $dto->toArray();

            if (!empty($dto->name)) {
                $data['name'] = $this->translation->translateAll($dto->name);
            }

            if (!empty($dto->description)) {
                $data['description'] = $this->translation->translateAll($dto->description);
            }

            $this->categoryDAO->update($category, $data);
            return $category->refresh();
        });
    }

    public function deleteCategory(int $id): bool
    {
        $category = $this->findById($id);
        return $this->categoryDAO->destroy($category);
    }
}
