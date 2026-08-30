<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    private const PERMISSIONS = [
        'universities.manage',
        'companies.manage',
        'admin-users.manage',
        'faculties.manage',
        'academic-years.manage',
        'students.manage',
        'palmares.import',
        'signatures.manage',
        'documents.revoke',
        'candidates.search',
        'documents.generate',
    ];

    public function run(): void
    {
        foreach (self::PERMISSIONS as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        $superAdmin = Role::findOrCreate('super_admin', 'web');
        $superAdmin->syncPermissions(self::PERMISSIONS);

        $universityAdmin = Role::findOrCreate('university_admin', 'web');
        $universityAdmin->syncPermissions([
            'faculties.manage',
            'academic-years.manage',
            'students.manage',
            'palmares.import',
            'signatures.manage',
            'documents.revoke',
        ]);

        $companyUser = Role::findOrCreate('company_user', 'web');
        $companyUser->syncPermissions([
            'candidates.search',
            'documents.generate',
        ]);

        Role::findOrCreate('student', 'web');
    }
}
