<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $admins = [
            [
                'name' => 'Pimpinan',
                'username' => 'dishub',
                'email' => 'dishub383@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'pimpinan',
            ],
            [
                'name' => 'dishub',
                'username' => 'admin',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ],
            [
                'name' => 'Maruf',
                'username' => 'magang',
                'email' => 'user@example.com',
                'password' => Hash::make('password'),
                'role' => 'magang',
            ],
        ];

        foreach ($admins as $admin) {
            User::updateOrCreate(
                ['email' => $admin['email']],
                $admin
            );
        }
    }
}
