<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\UniversityController;
use App\Http\Controllers\Api\VerificationController;
use Illuminate\Support\Facades\Route;

Route::post('/auth/login', [AuthController::class, 'login'])->middleware('throttle:10,1');

Route::get('/verification/{token}', [VerificationController::class, 'status']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    Route::get('/universities', [UniversityController::class, 'index']);
    Route::get('/universities/{university}/students', [UniversityController::class, 'students']);
    Route::get('/students/{student}', [StudentController::class, 'show']);

    Route::post('/verification', [VerificationController::class, 'generate'])->middleware('throttle:30,1');
});
