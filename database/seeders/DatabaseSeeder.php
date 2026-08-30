<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RolePermissionSeeder::class);
        $this->call(SuperAdminSeeder::class);

        if (app()->environment(['local', 'testing'])) {
            $this->call(DemoUniversitySeeder::class);
        }
    }
}
