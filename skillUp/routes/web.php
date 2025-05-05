<?php

use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\JobOfferController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\SchoolProjectController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/auth', [AuthController::class, 'show'])->name('auth');
Route::post('/auth', [AuthController::class, 'show'])->name('auth');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register'])->name('register');

Route::get('/forgot-password', [PasswordResetController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLinkEmail'])->name('password.email');

Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('password.update');



Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/perfil', [DashboardController::class, 'profile'])->name('profile.index');

    Route::get('/gestion-proyectos-escolares', [SchoolProjectController::class, 'index'])->name('school.projects.index');
    Route::put('/school-projects/{id}', [SchoolProjectController::class, 'update'])->name('school.projects.update');
    Route::delete('/proyecto-escolar/{id}', [SchoolProjectController::class, 'destroy'])->name('school.projects.destroy');
    Route::get('/proyectos-escolares/{id}', [SchoolProjectController::class, 'show'])->name('school.projects.show');
    Route::post('/school-projects', [SchoolProjectController::class, 'store'])->name('school.projects.store');


    Route::get('/proyectos', [ProjectController::class, 'index'])->name('projects.index');
    Route::post('/proyectos', [ProjectController::class, 'store'])->name('projects.store');
    Route::get('/proyectos/{id}', [ProjectController::class, 'show'])->name('projects.show');
    Route::get('/tus-proyectos', [ProjectController::class, 'ownProjects'])->name('projects.ownProjects');
    Route::delete('/tus-proyectos/{id}', [ProjectController::class, 'destroy'])->name('projects.destroy');
    Route::put('/tus-proyectos/{id}', [ProjectController::class, 'update'])->name('projects.update');
    Route::delete('/imagen-proyecto/{project}', [ProjectController::class, 'deleteMainImage'])->name('projects.deleteMainImage');


    Route::put('/perfil/{id}', [UserController::class, 'update'])->name('user.update');

    // companyIndex because it's only the job offers from the company (different as normal index, from everyone)
    Route::get('/tus-ofertas', [JobOfferController::class, 'companyIndex'])->name('job.offers.company.index');
    Route::post('/tus-ofertas/crear', [JobOfferController::class, 'store'])->name('job.offers.store');
    Route::put('/ofertas/{id}', [JobOfferController::class, 'update'])->name('job.offers.update');
    Route::delete('/ofertas/{id}', [JobOfferController::class, 'destroy'])->name('job.offers.destroy');
    Route::get('/ofertas', [JobOfferController::class, 'index'])->name('job.offers.index');
    Route::get('/ofertas/{id}/detalles', [JobOfferController::class, 'show'])->name('job.offers.show');
    Route::post('/ofertas/{id}/detalles', [JobOfferController::class, 'show'])->name('job.offers.show');

    Route::post('/ofertas/aplicar', [ApplicationController::class, 'store'])->name('applications.store');
    Route::get('/candidatos/{id}', [ApplicationController::class, 'show'])->name('applications.show');
    Route::get('/candidatos', [ApplicationController::class, 'index'])->name('applications.index');
    Route::delete('/candidaturas/{id}', [ApplicationController::class, 'destroy'])->name('applications.destroy');
    Route::put('/candidaturas/{id}', [ApplicationController::class, 'update'])->name('applications.update');

    Route::get('/favoritos', [FavoriteController::class, 'index'])->name('favorites.index');
    Route::post('/favoritos', [FavoriteController::class, 'store'])->name('favorites.store');
    Route::delete('/favoritos/{id}', [FavoriteController::class, 'destroy'])->name('favorites.destroy');

    Route::post('/projects/{project}/rate', [RatingController::class, 'rateProject'])->name('projects.rate');
    Route::post('/school-projects/{schoolProject}/rate', [RatingController::class, 'rateSchoolProject'])->name('school-projects.rate');

    Route::post('/projects/{project}/comments', [CommentController::class, 'storeProjectComment'])->name('projects.comments.store');
    Route::post('/school-projects/{schoolProject}/comments', [CommentController::class, 'storeSchoolProjectComment'])->name('school-projects.comments.store');
    Route::get('/comments/{comment}/edit', [CommentController::class, 'edit'])->name('comments.edit');
    Route::put('/comments/{comment}', [CommentController::class, 'update'])->name('comments.update');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');

    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');



});