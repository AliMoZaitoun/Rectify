<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\Core\PermissionResource;
use App\Services\PermissionService;
use App\Traits\ResponseTrait;

class PermissionController extends Controller
{
    use ResponseTrait;
    public function __construct(
        private PermissionService $permissionService
    ) {}

    public function index()
    {
        $permissions = $this->permissionService->index();
        return $this->successCollection($permissions, PermissionResource::class);
    }
}
