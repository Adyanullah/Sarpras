<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            ['name' => 'Developer', 'email' => 'developer@example.com', 'role' => '1'],
            ['name' => 'Admin', 'email' => 'admin@example.com', 'role' => '1'],
            ['name' => 'Waka', 'email' => 'waka@example.com', 'role' => '2'],
            ['name' => 'Petugas', 'email' => 'petugas@example.com', 'role' => '3'],
            ['name' => 'Kepala Sekolah', 'email' => 'kepsek@example.com', 'role' => '4'],
        ];

        foreach ($users as $user) {
            User::create([
                'name' => $user['name'],
                'email' => $user['email'],
                'email_verified_at' => now(),
                'password' => Hash::make('123'),
                'role' => $user['role'],
                // photo dan remember_token tidak diisi
            ]);
        }
    }
}
