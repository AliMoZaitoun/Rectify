<?php

namespace App\Services;

use App\DAO\PermissionDAO;

class PermissionService
{
    public function __construct(
        private PermissionDAO $permissionDAO
    ) {}

    public function index()
    {
        return $this->permissionDAO->index();
    }
}
