<?php

namespace Database\Seeders;

use App\Models\AdminMenu;
use App\Models\AdminRole;
use App\Models\AdminRoleMenu;
use App\Models\AdminRolePermission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AdminMenu::create([
            'parent_id' => 0,
            'order' => 0,
            'title' => 'Users (clients) Lists',
            'icon' => 'fa-android',
            'uri' => 'users-clients',
            'permission' => '*'
        ]);

        AdminMenu::create([
            'parent_id' => 0,
            'order' => 0,
            'title' => 'Posts Lists',
            'icon' => 'fa-cart-arrow-down',
            'uri' => 'posts',
            'permission' => '*'
        ]);

        AdminRole::create([
            'name' => 'User',
            'slug' => 'user'
        ]);

        AdminRoleMenu::create([
            'role_id' => 1,
            'menu_id' => 8
        ]);

        AdminRolePermission::create([
            'role_id' => 2,
            'permission_id' => 1
        ]);
    }
}
