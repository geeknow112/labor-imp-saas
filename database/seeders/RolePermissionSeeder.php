<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // 権限を作成
        $permissions = [
            ['name' => 'manage_users', 'description' => 'ユーザーの作成、編集、削除'],
            ['name' => 'manage_roles', 'description' => 'ロールの作成、編集、削除'],
            ['name' => 'manage_permissions', 'description' => '権限の作成、編集、削除'],
            ['name' => 'manage_blog', 'description' => 'ブログ記事の作成、編集、削除'],
            ['name' => 'view_dashboard', 'description' => '管理画面ダッシュボードの閲覧'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name']],
                $permission
            );
        }

        // ロールを作成
        $adminRole = Role::firstOrCreate(
            ['name' => 'admin'],
            [
                'description' => 'システム全体の管理権限'
            ]
        );

        $managerRole = Role::firstOrCreate(
            ['name' => 'manager'],
            [
                'description' => '限定的な管理権限'
            ]
        );

        $userRole = Role::firstOrCreate(
            ['name' => 'user'],
            [
                'description' => '基本的な閲覧権限'
            ]
        );

        // 管理者に全権限を付与
        $adminRole->permissions()->sync(Permission::all());

        // マネージャーに限定権限を付与
        $managerPermissions = Permission::whereIn('name', [
            'manage_blog',
            'view_dashboard'
        ])->get();
        $managerRole->permissions()->sync($managerPermissions);

        // 一般ユーザーに基本権限を付与
        $userPermissions = Permission::whereIn('name', [
            'view_dashboard'
        ])->get();
        $userRole->permissions()->sync($userPermissions);

        // デフォルトユーザーを作成
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@test.com'],
            [
                'name' => 'Admin',
                'password' => bcrypt('password')
            ]
        );

        // 管理者ロールを付与
        $adminUser->roles()->sync([$adminRole->id]);
    }
}