<?php

namespace App\Http\Controllers\V1\Core;

use App\DTOs\Core\Create\CreateBranchDTO;
use App\DTOs\Core\Update\UpdateBranchDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Core\CreateBranchRequest;
use App\Http\Resources\V1\Core\BranchResource;
use App\Services\Core\BranchService;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    use ResponseTrait;

    public function __construct(
        private BranchService $BranchService
    ) {}

    public function index()
    {
        $items = $this->BranchService->index();
        return $this->successCollection($items, BranchResource::class);
    }

    public function store(CreateBranchRequest $request)
    {
        $BranchDTO = CreateBranchDTO::fromRequest($request->validated());

        $item = $this->BranchService->store($BranchDTO);
        return $this->useResource($item, BranchResource::class, __('messages.common.stored'), 201);
    }

    public function show(int $id)
    {
        $item = $this->BranchService->show($id);
        return $this->useResource($item, BranchResource::class);
    }

    public function update(int $id, Request $request)
    {
        $BranchDTO = UpdateBranchDTO::fromRequest($request->all());

        $updatedItem = $this->BranchService->update($id, $BranchDTO);
        return $this->useResource($updatedItem, BranchResource::class, __('messages.common.updated'));
    }

    public function destroy(int $id)
    {
        $this->BranchService->destroy($id);
        return $this->successResponse([], __('messages.common.deleted'));
    }
}
