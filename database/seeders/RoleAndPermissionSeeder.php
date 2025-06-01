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
        $guards = ['web', 'api'];

     
        $roles = ['super admin', 'student', 'teacher', 'parent'];
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

        foreach ($guards as $guard) {
            $admin = null;
            foreach ($roles as $role) {
                $roleModel = Role::firstOrCreate([
                    'name' => $role,
                    'guard_name' => $guard
                ]);

                if ($role === 'super admin') {
                    $admin = $roleModel;
                }
            }

            $permissionModels = [];
            foreach ($permissions as $permission) {
                $perm = Permission::firstOrCreate([
                    'name' => $permission,
                    'guard_name' => $guard
                ]);
                $permissionModels[] = $perm->name;
            }

            if ($admin) {
                $admin->syncPermissions($permissionModels);
            }
        }

        $this->command->info('Roles and permissions seeded successfully for web and api guards.');
    }
}
