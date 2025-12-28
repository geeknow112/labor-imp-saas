<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions
        $permissions = [
            ['name' => 'manage_users', 'description' => 'Manage users'],
            ['name' => 'manage_roles', 'description' => 'Manage roles and permissions'],
            ['name' => 'manage_tenants', 'description' => 'Manage tenants'],
            ['name' => 'view_dashboard', 'description' => 'View admin dashboard'],
            ['name' => 'manage_settings', 'description' => 'Manage system settings'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission['name']], $permission);
        }

        // Create roles
        $adminRole = Role::firstOrCreate(
            ['name' => 'admin'],
            ['description' => 'System Administrator']
        );

        $managerRole = Role::firstOrCreate(
            ['name' => 'manager'],
            ['description' => 'Manager']
        );

        $userRole = Role::firstOrCreate(
            ['name' => 'user'],
            ['description' => 'Regular User']
        );

        // Assign permissions to roles
        $adminRole->permissions()->sync(Permission::all());
        $managerRole->permissions()->sync(
            Permission::whereIn('name', ['manage_users', 'view_dashboard'])->get()
        );
        $userRole->permissions()->sync(
            Permission::where('name', 'view_dashboard')->get()
        );
    }
}