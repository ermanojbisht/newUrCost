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

        $permissions = [
            'user-list',
            'user-create',
            'user-edit',
            'user-delete',
            'role-list',
            'role-create',
            'role-edit',
            'role-delete',
            'permission-list',
            'permission-create',
            'permission-edit',
            'permission-delete',
            'view units',
            'create units',
            'edit units',
            'delete units',
            'view unitgroups',
            'create unitgroups',
            'edit unitgroups',
            'delete unitgroups',
            'view resourcegroups',
            'create resourcegroups',
            'edit resourcegroups',
            'delete resourcegroups',
            'view truckspeeds',
            'create truckspeeds',
            'edit truckspeeds',
            'delete truckspeeds',
            'view sors',
            'create sors',
            'edit sors',
            'delete sors',
            'view resourcecapacityrules',
            'create resourcecapacityrules',
            'edit resourcecapacityrules',
            'delete resourcecapacityrules',
            'view polskeletons',
            'create polskeletons',
            'edit polskeletons',
            'delete polskeletons',
            'view polrates',
            'create polrates',
            'edit polrates',
            'delete polrates',
            'view ratecards',
            'create ratecards',
            'edit ratecards',
            'delete ratecards',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // create roles and assign created permissions

        $role = Role::firstOrCreate(['name' => 'user-manager']);
        $role->givePermissionTo([
            'user-list',
            'user-create',
            'user-edit',
            'user-delete',
            'role-list',
            'role-create',
            'role-edit',
            'role-delete',
            'permission-list',
            'permission-create',
            'permission-edit',
            'permission-delete',
        ]);

        $sorAdminRole = Role::firstOrCreate(['name' => 'sor-admin']);
        $sorAdminRole->givePermissionTo([
            'view units',
            'create units',
            'edit units',
            'delete units',
            'view unitgroups',
            'create unitgroups',
            'edit unitgroups',
            'delete unitgroups',
            'view resourcegroups',
            'create resourcegroups',
            'edit resourcegroups',
            'delete resourcegroups',
            'view truckspeeds',
            'create truckspeeds',
            'edit truckspeeds',
            'delete truckspeeds',
            'view sors',
            'create sors',
            'edit sors',
            'delete sors',
            'view resourcecapacityrules',
            'create resourcecapacityrules',
            'edit resourcecapacityrules',
            'delete resourcecapacityrules',
            'view polskeletons',
            'create polskeletons',
            'edit polskeletons',
            'delete polskeletons',
            'view polrates',
            'create polrates',
            'edit polrates',
            'delete polrates',
            'view ratecards',
            'create ratecards',
            'edit ratecards',
            'delete ratecards',
        ]);
    }
}
