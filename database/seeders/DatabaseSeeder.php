<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $user = Role::findOrCreate('user', 'web');
        $admin = Role::findOrCreate('admin', 'web');

        $managePromos = Permission::findOrCreate("manage-promos");
        $admin->givePermissionTo($managePromos);


        $adminModel = User::factory([
            'name' => 'Admin',
            'email' => 'admin@mail.ru',
            'password' => bcrypt('initial_password'),
        ])->create();

        $adminModel->assignRole('admin');
        $adminModel->save();

        User::factory(10)->create();

    }
}
