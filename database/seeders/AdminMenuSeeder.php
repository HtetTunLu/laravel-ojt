<?php

namespace Database\Seeders;

use App\Models\AdminMenu;
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

    }
}
