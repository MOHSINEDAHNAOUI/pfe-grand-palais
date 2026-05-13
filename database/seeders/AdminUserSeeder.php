<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name'              => 'Super Admin',
                'email'             => 'admin@hotel.com',
                'password'          => Hash::make('password'),
                'email_verified_at' => now(),
                'role'              => 'admin',
            ],
            [
                'name'              => 'Manager Hotel',
                'email'             => 'manager@hotel.com',
                'password'          => Hash::make('password'),
                'email_verified_at' => now(),
                'role'              => 'manager',
            ],
            [
                'name'              => 'Réceptionniste',
                'email'             => 'reception@hotel.com',
                'password'          => Hash::make('password'),
                'email_verified_at' => now(),
                'role'              => 'receptionist',
            ],
            [
                'name'              => 'Client Test',
                'email'             => 'client@hotel.com',
                'password'          => Hash::make('password'),
                'email_verified_at' => now(),
                'role'              => 'client',
            ],
        ];

        foreach ($users as $userData) {
            $role = $userData['role'];
            unset($userData['role']);

            $user = User::updateOrCreate(['email' => $userData['email']], $userData);
            $user->syncRoles([$role]);
        }
    }
}