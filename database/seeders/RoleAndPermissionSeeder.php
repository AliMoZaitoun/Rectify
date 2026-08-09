<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $resources = [
            'client',
            'employee',
            'branch',
            'role',
            'permission',

            'complaint',
            'category',
            'location'
        ];

        $actions = ['read', 'create', 'update', 'archive', 'restore', 'delete'];

        foreach ($resources as $resource) {
            foreach ($actions as $action) {
                Permission::create(['name' => "$action.$resource"]);
            }
        }

        // Admin — gets everything
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions(Permission::all());

        // Employee
        $staff = Role::firstOrCreate(['name' => 'staff']);
        $staff->syncPermissions([
            'read.client',
            'read.branch',
            'read.complaint',
            'update.complaint',
            'read.location'
        ]);

        $manager = Role::firstOrCreate(['name' => 'manager']);
        $manager->syncPermissions([
            'update.branch',
            'create.category',
            'read.category',
            'update.category',
            'delete.category',
            'create.employee',
            'read.employee',
            'update.employee',
            'delete.employee'
        ]);

        // Client
        $client = Role::firstOrCreate(['name' => 'client']);
        $client->syncPermissions([
            'read.branch',
            'create.complaint'
        ]);
    }
}
