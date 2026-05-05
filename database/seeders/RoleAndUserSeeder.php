<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class RoleAndUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define Roles
        $roles = [
            ['name' => 'admin',    'applicable_for_shop' => false],
            ['name' => 'seller',   'applicable_for_shop' => true],
            ['name' => 'customer', 'applicable_for_shop' => false],
            ['name' => 'manager',  'applicable_for_shop' => true],
            ['name' => 'employee', 'applicable_for_shop' => true],
        ];

        foreach ($roles as $roleData) {
            Role::updateOrCreate(['name' => $roleData['name']], $roleData);
        }

        // Define Users
        $users = [
            [
                'name'     => 'Admin User',
                'email'    => 'admin@example.com',
                'password' => Hash::make('password'),
                'role'     => 'admin',
                'role_id'  => Role::where('name', 'admin')->first()->id,
            ],
            [
                'name'     => 'Seller User',
                'email'    => 'seller@example.com',
                'password' => Hash::make('password'),
                'role'     => 'seller',
                'role_id'  => Role::where('name', 'seller')->first()->id,
            ],
            [
                'name'     => 'Customer User',
                'email'    => 'customer@example.com',
                'password' => Hash::make('password'),
                'role'     => 'customer',
                'role_id'  => Role::where('name', 'customer')->first()->id,
            ],
            [
                'name'     => 'Manager User',
                'email'    => 'manager@example.com',
                'password' => Hash::make('password'),
                'role'     => 'manager',
                'role_id'  => Role::where('name', 'manager')->first()->id,
            ],
            [
                'name'     => 'Employee User',
                'email'    => 'employee@example.com',
                'password' => Hash::make('password'),
                'role'     => 'employee',
                'role_id'  => Role::where('name', 'employee')->first()->id,
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(['email' => $userData['email']], $userData);
        }
    }
}
