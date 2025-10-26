<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()['cache']->forget('spatie.permission.cache');

        // create permissions
        Permission::create(['name' => 'user-list']);
        Permission::create(['name' => 'user-create']);
        Permission::create(['name' => 'user-edit']);
        Permission::create(['name' => 'user-delete']);
        Permission::create(['name' => 'role-list']);
        Permission::create(['name' => 'role-create']);
        Permission::create(['name' => 'role-edit']);
        Permission::create(['name' => 'role-delete']);
        Permission::create(['name' => 'permission-list']);
        Permission::create(['name' => 'permission-create']);
        Permission::create(['name' => 'permission-edit']);
        Permission::create(['name' => 'permission-delete']);

        // create roles and assign created permissions

        $role = Role::create(['name' => 'user-manager']);
        $role->givePermissionTo('user-list');
        $role->givePermissionTo('user-create');
        $role->givePermissionTo('user-edit');
        $role->givePermissionTo('user-delete');
        $role->givePermissionTo('role-list');
        $role->givePermissionTo('role-create');
        $role->givePermissionTo('role-edit');
        $role->givePermissionTo('role-delete');
        $role->givePermissionTo('permission-list');
        $role->givePermissionTo('permission-create');
        $role->givePermissionTo('permission-edit');
        $role->givePermissionTo('permission-delete');

    }
}
