<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Roles if they don't exist
        $roles = [
            'super admin',
            'student',
            'teacher',
            'parent'
        ];

        $admin = null;
        foreach ($roles as $role) {
            $roleModel = Role::firstOrCreate(['name' => $role]);
            if ($role === 'super admin') {
                $admin = $roleModel;
            }
        }

        // Create Permissions if they don't exist
        $permissions = [
            'create role',
            'create permission',
            'assign permissions',
            'create subjects',
            'create classes',
            'assign student class',
            'see student list',
            'see student details',
            'edit student details',
            'delete student',
            'create exam',
            'see exam results',
            'edit exam results',
            'delete exam',
        ];

        $permissionModels = [];
        foreach ($permissions as $permission) {
            $permissionModels[] = Permission::firstOrCreate(['name' => $permission])->name;
        }

        // Assign All Permissions to Super Admin if not already assigned
        if ($admin) {
            $admin->syncPermissions($permissionModels);
        }

        $this->command->info('Roles and permissions seeded successfully.');
    }
}
