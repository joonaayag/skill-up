<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SchoolProjectController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/auth', [AuthController::class, 'show'])->name('auth');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register'])->name('register');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'show'])->name('dashboard');

    Route::get('/gestion-proyectos-escolares', [SchoolProjectController::class, 'index'])->name('school.projects.index');
    Route::put('/school-projects/{id}', [SchoolProjectController::class, 'update'])->name('school.projects.update');
    Route::delete('/proyecto-escolar/{id}', [SchoolProjectController::class, 'destroy'])->name('school.projects.destroy');
});