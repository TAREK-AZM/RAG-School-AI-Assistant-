<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Create an admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@school.edu',
            'password' => Hash::make('password'),
            'is_admin' => true,
        ]);

        // Create a regular user if needed
        User::create([
            'name' => 'Teacher User',
            'email' => 'teacher@school.edu',
            'password' => Hash::make('password'),
            'is_admin' => false,
        ]);
    }
}