<?php

use App\Http\Controllers\Company\HistoryController;
use App\Http\Controllers\Company\SearchController;
use App\Http\Controllers\Company\VerificationController as CompanyVerificationController;
use App\Http\Controllers\Public\PublicVerificationController;
use App\Http\Controllers\SuperAdmin\AdminUserController;
use App\Http\Controllers\SuperAdmin\CompanyController as SuperAdminCompanyController;
use App\Http\Controllers\SuperAdmin\UniversityController as SuperAdminUniversityController;
use App\Http\Controllers\University\AcademicYearController;
use App\Http\Controllers\University\DashboardController;
use App\Http\Controllers\University\DigitalSignatureController;
use App\Http\Controllers\University\FacultyController;
use App\Http\Controllers\University\PalmaresImportController;
use App\Http\Controllers\University\StudentController;
use App\Http\Controllers\University\VerificationDocumentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $user = auth()->user();

    if (! $user) {
        return redirect()->route('login');
    }

    return match (true) {
        $user->isSuperAdmin() => redirect()->route('superadmin.universities.index'),
        $user->isUniversityAdmin() => redirect()->route('university.dashboard'),
        $user->isCompanyUser() => redirect()->route('company.search.create'),
        default => redirect()->route('login'),
    };
})->name('home');

// Page publique atteinte en scannant un QR Code — aucune authentification,
// aucune donnée personnelle transmise autrement que via le jeton opaque.
Route::get('/verify/{token}', [PublicVerificationController::class, 'show'])->name('public.verify');

require __DIR__.'/auth.php';

Route::middleware(['auth', 'active', 'university.scope'])
    ->prefix('universite')
    ->name('university.')
    ->group(function () {
        Route::get('/dashboard', DashboardController::class)->name('dashboard');

        Route::get('/facultes', [FacultyController::class, 'index'])->name('faculties.index');
        Route::post('/facultes', [FacultyController::class, 'store'])->name('faculties.store');
        Route::put('/facultes/{faculty}', [FacultyController::class, 'update'])->name('faculties.update');

        Route::get('/annees-academiques', [AcademicYearController::class, 'index'])->name('academic-years.index');
        Route::post('/annees-academiques', [AcademicYearController::class, 'store'])->name('academic-years.store');
        Route::post('/annees-academiques/{academicYear}/cloturer', [AcademicYearController::class, 'close'])->name('academic-years.close');

        Route::resource('etudiants', StudentController::class)
            ->names('students')
            ->parameters(['etudiants' => 'student'])
            ->except('destroy');
        Route::post('/etudiants/{student}/desactiver', [StudentController::class, 'deactivate'])->name('students.deactivate');

        Route::get('/palmares', [PalmaresImportController::class, 'index'])->name('palmares.index');
        Route::get('/palmares/importer', [PalmaresImportController::class, 'create'])->name('palmares.create');
        Route::post('/palmares/apercu', [PalmaresImportController::class, 'preview'])->name('palmares.preview');
        Route::post('/palmares', [PalmaresImportController::class, 'store'])->name('palmares.store');
        Route::get('/palmares/{palmares}', [PalmaresImportController::class, 'show'])->name('palmares.show');

        Route::get('/signature', [DigitalSignatureController::class, 'edit'])->name('signature.edit');
        Route::post('/signature', [DigitalSignatureController::class, 'store'])->name('signature.store');
        Route::post('/signature/{signature}/revoquer', [DigitalSignatureController::class, 'revoke'])->name('signature.revoke');

        Route::get('/documents', [VerificationDocumentController::class, 'index'])->name('documents.index');
        Route::post('/documents/{document}/revoquer', [VerificationDocumentController::class, 'revoke'])->name('documents.revoke');
    });

Route::middleware(['auth', 'active', 'role:super_admin'])
    ->prefix('admin')
    ->name('superadmin.')
    ->group(function () {
        Route::get('/universites', [SuperAdminUniversityController::class, 'index'])->name('universities.index');
        Route::post('/universites', [SuperAdminUniversityController::class, 'store'])->name('universities.store');
        Route::post('/universites/{university}/suspendre', [SuperAdminUniversityController::class, 'suspend'])->name('universities.suspend');
        Route::post('/universites/{university}/reactiver', [SuperAdminUniversityController::class, 'reactivate'])->name('universities.reactivate');

        Route::get('/entreprises', [SuperAdminCompanyController::class, 'index'])->name('companies.index');
        Route::post('/entreprises', [SuperAdminCompanyController::class, 'store'])->name('companies.store');
        Route::post('/entreprises/{company}/suspendre', [SuperAdminCompanyController::class, 'suspend'])->name('companies.suspend');

        Route::get('/comptes', [AdminUserController::class, 'index'])->name('admin-users.index');
        Route::post('/comptes', [AdminUserController::class, 'store'])->name('admin-users.store');
        Route::post('/comptes/{user}/suspendre', [AdminUserController::class, 'suspend'])->name('admin-users.suspend');
    });

Route::middleware(['auth', 'active', 'company.scope'])
    ->prefix('entreprise')
    ->name('company.')
    ->group(function () {
        Route::get('/recherche', [SearchController::class, 'create'])->name('search.create');
        Route::get('/recherche/detaillee', [SearchController::class, 'detailed'])->name('search.detailed');
        Route::get('/recherche/rapide', [SearchController::class, 'quick'])->name('search.quick');

        Route::get('/candidats/{student}', [CompanyVerificationController::class, 'show'])->name('candidate.show');
        Route::post('/verifications', [CompanyVerificationController::class, 'generate'])->name('verification.generate');
        Route::get('/verifications/{document}/telecharger', [CompanyVerificationController::class, 'download'])->name('verification.download');

        Route::get('/historique', HistoryController::class)->name('history');
    });
