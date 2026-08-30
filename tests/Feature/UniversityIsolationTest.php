<?php

use App\Models\Faculty;
use App\Models\Student;
use App\Models\University;
use App\Models\User;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::findOrCreate('university_admin', 'web');
});

/**
 * Guards the single most important security property from the cahier des
 * charges (§17) : "Une université ne doit jamais pouvoir accéder aux
 * données d'une autre université." This is the automated check backing
 * that promise — not just a policy that happens to be wired correctly.
 */
it('never lists another university\'s students in a scoped index', function () {
    $universityA = University::factory()->create();
    $universityB = University::factory()->create();

    $facultyA = Faculty::factory()->for($universityA)->create();
    $facultyB = Faculty::factory()->for($universityB)->create();

    $studentA = Student::factory()->for($universityA)->for($facultyA, 'faculty')->create();
    $studentB = Student::factory()->for($universityB)->for($facultyB, 'faculty')->create();

    $adminA = User::factory()->create(['university_id' => $universityA->id, 'status' => 'active']);
    $adminA->assignRole('university_admin');

    $response = $this->actingAs($adminA)->get(route('university.students.index'));

    $response->assertOk();
    $response->assertSee($studentA->unique_student_id);
    $response->assertDontSee($studentB->unique_student_id);
});

it('returns 404 — not the other university\'s data — for a direct student lookup outside the tenant', function () {
    $universityA = University::factory()->create();
    $universityB = University::factory()->create();

    $facultyB = Faculty::factory()->for($universityB)->create();
    $studentB = Student::factory()->for($universityB)->for($facultyB, 'faculty')->create();

    $adminA = User::factory()->create(['university_id' => $universityA->id, 'status' => 'active']);
    $adminA->assignRole('university_admin');

    $this->actingAs($adminA)
        ->get(route('university.students.show', $studentB))
        ->assertNotFound();
});

it('lets the super admin see students across every university', function () {
    $universityA = University::factory()->create();
    $facultyA = Faculty::factory()->for($universityA)->create();
    $studentA = Student::factory()->for($universityA)->for($facultyA, 'faculty')->create();

    Role::findOrCreate('super_admin', 'web');
    $superAdmin = User::factory()->create(['status' => 'active']);
    $superAdmin->assignRole('super_admin');

    $this->actingAs($superAdmin);

    expect(Student::query()->where('id', $studentA->id)->exists())->toBeTrue();
});
