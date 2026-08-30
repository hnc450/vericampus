<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Company;
use App\Models\Degree;
use App\Models\DigitalSignature;
use App\Models\Faculty;
use App\Models\Promotion;
use App\Models\Student;
use App\Models\StudentAcademicRecord;
use App\Models\University;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

/**
 * Jeu de données de démonstration — une université, une entreprise, quelques
 * étudiants — pour explorer l'application sans avoir à tout saisir à la main.
 * À ne PAS exécuter en production (voir DatabaseSeeder).
 */
class DemoUniversitySeeder extends Seeder
{
    public function run(): void
    {
        $university = University::firstOrCreate(
            ['short_name' => 'UNIKIN-DEMO'],
            [
                'name' => 'Université de Kinshasa (démo)',
                'city' => 'Kinshasa',
                'country' => 'RD Congo',
                'status' => 'active',
            ]
        );

        $admin = User::firstOrCreate(
            ['email' => 'admin@unikin-demo.test'],
            [
                'name' => 'Admin UNIKIN',
                'password' => Hash::make('password'),
                'university_id' => $university->id,
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );
        $admin->hasRole('university_admin') || $admin->assignRole('university_admin');

        $faculty = Faculty::firstOrCreate(
            ['university_id' => $university->id, 'code' => 'SC-ECO'],
            ['name' => 'Sciences économiques et de gestion', 'status' => 'active']
        );

        $academicYear = AcademicYear::firstOrCreate(
            ['university_id' => $university->id, 'label' => '2024-2025'],
            ['start_date' => '2024-10-01', 'end_date' => '2025-07-31', 'status' => 'active']
        );

        $promotion = Promotion::firstOrCreate(
            ['faculty_id' => $faculty->id, 'academic_year_id' => $academicYear->id, 'name' => 'Licence 3 — Promotion A'],
            ['level' => 'L3']
        );

        $degree = Degree::firstOrCreate(
            ['university_id' => $university->id, 'faculty_id' => $faculty->id, 'name' => 'Licence en Sciences économiques'],
            ['level' => 'Licence']
        );

        $firstStudent = null;

        foreach ([
            ['MBOMBO', 'Kalonji', 'Jérémie', '2024EC0001'],
            ['MBOMBO', null, 'Grace', '2024EC0002'],
            ['KABEYA', 'Ntumba', 'Patrick', '2024EC0003'],
        ] as [$lastName, $middleName, $firstName, $matricule]) {
            $student = Student::firstOrCreate(
                ['university_id' => $university->id, 'student_number' => $matricule],
                [
                    'faculty_id' => $faculty->id,
                    'unique_student_id' => Student::generateUniqueStudentId($university),
                    'last_name' => $lastName,
                    'middle_name' => $middleName,
                    'first_name' => $firstName,
                    'status' => 'active',
                ]
            );

            $firstStudent ??= $student;
        }

        // Un dossier déjà validé, pour pouvoir tester la génération de
        // document de vérification de bout en bout dès le seed.
        if ($firstStudent) {
            StudentAcademicRecord::firstOrCreate(
                ['student_id' => $firstStudent->id, 'academic_year_id' => $academicYear->id],
                [
                    'promotion_id' => $promotion->id,
                    'degree_id' => $degree->id,
                    'mention' => 'Distinction',
                    'average' => 72.4,
                    'status' => 'validated',
                    'graduation_date' => now()->subMonth(),
                ]
            );
        }

        if (! $university->activeSignature()) {
            $onePixelPng = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=');
            $path = 'signatures/'.$university->id.'/demo-signature.png';
            Storage::disk('signatures')->put($path, $onePixelPng);

            DigitalSignature::create([
                'university_id' => $university->id,
                'authority_name' => 'Prof. Jean Kalonji',
                'authority_title' => 'Recteur',
                'signature_image_path' => $path,
                'valid_from' => now()->subMonths(6),
                'status' => 'active',
                'created_by' => $admin->id,
            ]);
        }

        $company = Company::firstOrCreate(
            ['name' => 'Demo RH Solutions'],
            ['sector' => 'Ressources humaines', 'status' => 'active']
        );

        $companyUser = User::firstOrCreate(
            ['email' => 'rh@demo-rh.test'],
            [
                'name' => 'Recruteur Demo',
                'password' => Hash::make('password'),
                'company_id' => $company->id,
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );
        $companyUser->hasRole('company_user') || $companyUser->assignRole('company_user');
    }
}
