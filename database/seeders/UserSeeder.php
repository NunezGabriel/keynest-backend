<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 3 Seekers
        User::factory()->count(3)->state([
            'user_type' => 'seeker',
        ])->create();

        // 2 Landlords
        User::factory()->count(2)->state([
            'user_type' => 'landlord',
        ])->create();

        // 1 Admin
        User::create([
            'id' => Str::uuid(),
            'name' => 'Admin',
            'email' => 'admin@keynest.com',
            'password' => Hash::make('admin123'),
            'user_type' => 'admin',
        ]);
    }
}
