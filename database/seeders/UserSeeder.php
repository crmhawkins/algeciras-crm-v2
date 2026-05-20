<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::updateOrCreate(
            ['email' => 'admin@algecirascf.es'],
            [
                'name' => 'Administrador ACF',
                'password' => Hash::make('AdminAcf2026!'),
                'email_verified_at' => now(),
            ],
        );

        if (! $user->hasRole('super-admin')) {
            $user->assignRole('super-admin');
        }
    }
}
