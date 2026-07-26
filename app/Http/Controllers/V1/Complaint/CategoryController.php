<?php

namespace App\Http\Controllers\V1\Complaint;

use App\DTOs\Complaint\Create\CategoryDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Complaint\StoreCategoryRequest;
use App\Http\Requests\V1\Complaint\UpdateCategoryRequest;
use App\Http\Resources\V1\Complaint\CategoryResource;
use App\Services\Complaint\CategoryService;
use App\Traits\ResponseTrait;

class CategoryController extends Controller
{
    use ResponseTrait;

    public function __construct(
        private CategoryService $service
    ) {}

    public function index()
    {
        $categories = $this->service->getAll();
        return $this->successCollection($categories, CategoryResource::class);
    }

    public function show(int $id)
    {
        $category = $this->service->findById($id);
        return $this->useResource($category, CategoryResource::class);
    }

    public function store(StoreCategoryRequest $request)
    {
        $dto = CategoryDTO::fromStoreRequest($request);
        $category = $this->service->createCategory($dto);

        return $this->useResource($category, CategoryResource::class, __('messages.common.stored'));
    }

    public function update(UpdateCategoryRequest $request, int $id)
    {
        $dto = CategoryDTO::fromUpdateRequest($request);

        $category = $this->service->updateCategory($id, $dto);

        return $this->useResource($category, CategoryResource::class, __('messages.common.updated'));
    }

    public function destroy(int $id)
    {
        $this->service->deleteCategory($id);
        return $this->successResponse([], __('messages.common.deleted'));
    }
}
