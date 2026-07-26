<?php

namespace App\DAO\Complaint;

use App\Models\Complaint\Category;
use Illuminate\Database\Eloquent\Collection;

class CategoryDAO
{
    public function all(): Collection
    {
        return Category::all();
    }

    public function byId(int $id): ?Category
    {
        return Category::find($id);
    }

    public function store(array $data): Category
    {
        return Category::create($data);
    }

    public function update(Category $category, array $data): bool
    {
        return $category->update($data);
    }

    public function destroy(Category $category): bool
    {
        return $category->delete();
    }
}
