<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define permissions name and permissions in config/permissions.php
        $permissionsName = config('permissions');

        foreach ($permissionsName as $permissionGroup) {
            $groupName = $permissionGroup['group_name'];
            foreach ($permissionGroup['permissions'] as $permission) {
                Permission::updateOrCreate([
                    'name' => $groupName . '.' . $permission,
                ], [
                    'guard_name' => 'web'
                ]);
            }
        }

        $superadminRole = Role::updateOrCreate([
            'name' => 'Superadmin'
        ], [
            'guard_name' => 'web'
        ]);

        $superadminRole->givePermissionTo(Permission::all());

        $viewerRole = Role::updateOrCreate([
            'name' => 'Viewer'
        ], [
            'guard_name' => 'web'
        ]);
    }
}
