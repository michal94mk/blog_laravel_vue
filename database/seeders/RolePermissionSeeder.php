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
            [
                'name' => 'manage_posts',
                'display_name' => 'Manage Posts',
                'description' => 'Can create, edit, and delete posts'
            ],
            [
                'name' => 'manage_comments',
                'display_name' => 'Manage Comments',
                'description' => 'Can create, edit, and delete comments'
            ],
            [
                'name' => 'manage_users',
                'display_name' => 'Manage Users',
                'description' => 'Can manage user accounts and roles'
            ],
        ];

        foreach ($permissions as $permission) {
            Permission::create($permission);
        }

        // Create roles
        $roles = [
            [
                'name' => 'admin',
                'display_name' => 'Administrator',
                'description' => 'Full access to all features'
            ],
            [
                'name' => 'user',
                'display_name' => 'User',
                'description' => 'Basic user access'
            ],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }

        // Assign permissions to roles
        $adminRole = Role::where('name', 'admin')->first();
        $userRole = Role::where('name', 'user')->first();

        // Admin gets all permissions
        $adminRole->permissions()->attach(Permission::all());

        // User gets basic permissions
        $userPermissions = Permission::whereIn('name', ['manage_posts', 'manage_comments'])->get();
        $userRole->permissions()->attach($userPermissions);
    }
}
