<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('SUPER_ADMIN_EMAIL', 'superadmin@vericampus.test');
        $password = env('SUPER_ADMIN_PASSWORD');

        if (! $password) {
            $password = Str::password(16);
            $this->command?->warn("SUPER_ADMIN_PASSWORD non défini dans .env — mot de passe généré : {$password}");
        }

        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => 'Super Administrateur',
                'password' => Hash::make($password),
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        if (! $user->hasRole('super_admin')) {
            $user->assignRole('super_admin');
        }
    }
}
