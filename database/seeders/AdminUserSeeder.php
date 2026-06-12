<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@albionhub.com'],
            [
                'name'     => 'Admin',
                'password' => Hash::make('admin123'),
                'is_admin' => true,
            ]
        );
        User::updateOrCreate(
            ['email' => 'alby007.44@gmail.com'],
            [
                'name'     => 'Admin',
                'password' => Hash::make('blasterzinho123'),
                'is_admin' => true,
            ]
        );
    }
}
