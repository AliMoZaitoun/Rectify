<?php

namespace App\DAO;

use Spatie\Permission\Models\Permission;

class PermissionDAO
{
    public function __construct(
        private UserDAO $userDAO
    ) {}

    public function index()
    {
        return Permission::all();
    }
}
