<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Kiril Draganov',
                'email' => 'kiril@admin.local',
                'password' => Hash::make('password123'),
                'role' => 'owner',
                'is_admin' => true,
            ],
            [
                'name' => 'Admin User',
                'email' => 'admin@admin.local',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'is_admin' => true,
            ],
            [
                'name' => 'Ivan Ivanov',
                'email' => 'ivan@backend.local',
                'password' => Hash::make('password123'),
                'role' => 'backend',
            ],
            [
                'name' => 'Vladimir Petrov',
                'email' => 'vladi@frontend.local',
                'password' => Hash::make('password123'),
                'role' => 'frontend',
            ],
            [
                'name' => 'Maria QA',
                'email' => 'maria@qa.local',
                'password' => Hash::make('password123'),
                'role' => 'qa',
            ],
            [
                'name' => 'Alex Designer',
                'email' => 'alex@designer.local',
                'password' => Hash::make('password123'),
                'role' => 'designer',
            ],
            [
                'name' => 'Nina Project Manager',
                'email' => 'nina@pm.local',
                'password' => Hash::make('password123'),
                'role' => 'project_manager',
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }
    }
}