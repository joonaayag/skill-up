<?php

use App\Http\Controllers\AdminController;
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
use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\Request;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;



Route::get('/auth/google/redirect', function () {
    return Socialite::driver('google')->redirect();
});

Route::get('/auth/google/callback', function () {
    $googleUser = Socialite::driver('google')->stateless()->user();

    $existingUser = User::where('email', $googleUser->getEmail())->first();

    if ($existingUser) {
        Auth::login($existingUser);
        return redirect('/dashboard')->with('message', __('messages.messages.register'));
    }

    Session::put('google_user', [
        'email' => $googleUser->getEmail(),
        'name' => $googleUser->getName(),
    ]);

    return redirect('/elegir-rol');
});

Route::get('/elegir-rol', function () {
    if (!Session::has('google_user')) {
        return redirect('/login');
    }
    return view('auth.choose_role');
});


Route::post('/elegir-rol', function (Request $request) {
    $messages = [
        'role.required' => __('messages.errors.role.required'),
        'role.in' => __('messages.errors.role.in'),

        'birth_date.required' => __('messages.errors.birth_date.required'),
        'birth_date.date' => __('messages.errors.birth_date.date'),
        'birth_date.before_or_equal' => __('messages.errors.birth_date.before_or_equal'),

        'current_course.required' => __('messages.errors.current_course.required'),
        'current_course.string' => __('messages.errors.current_course.string'),
        'current_course.max' => __('messages.errors.current_course.max'),

        'educational_center.required' => __('messages.errors.educational_center.required'),
        'educational_center.string' => __('messages.errors.educational_center.string'),
        'educational_center.max' => __('messages.errors.educational_center.max'),

        'cif.required' => __('messages.errors.cif.required'),
        'cif.string' => __('messages.errors.cif.string'),
        'cif.max' => __('messages.errors.cif.max'),

        'address.required' => __('messages.errors.address.required'),
        'address.string' => __('messages.errors.address.string'),
        'address.max' => __('messages.errors.address.max'),

        'sector.required' => __('messages.errors.sector.required'),
        'sector.string' => __('messages.errors.sector.string'),
        'sector.max' => __('messages.errors.sector.max'),

        'website.url' => __('messages.errors.website.url'),
        'website.max' => __('messages.errors.website.max'),
    ];

    $request->validate([
        'role' => 'required|in:Usuario,Alumno,Profesor,Empresa',
    ], $messages);

    $googleData = Session::get('google_user');

    if (!$googleData) {
        return redirect('/login')->withErrors([
            'session' => __('messages.errors.session.expired'),
        ]);
    }

    $nameParts = explode(' ', $googleData['name'], 2);
    $name = $nameParts[0];
    $lastName = $nameParts[1] ?? '';

    $user = User::create([
        'email' => $googleData['email'],
        'name' => $name,
        'last_name' => $lastName,
        'password' => bcrypt('Password1@'),
        'role' => $request->role,
    ]);

    $detail = UserDetail::create(['user_id' => $user->id]);

    if ($user->role === 'Alumno') {
        $request->validate([
            'birth_date' => 'required|date|before_or_equal:' . date('Y-m-d'),
            'current_course' => 'required|string|max:50',
            'educational_center' => 'required|string|max:100',
        ], $messages);

        $detail->update($request->only([
            'birth_date',
            'current_course',
            'educational_center',
        ]));
    }

    if ($user->role === 'Empresa') {
        $request->validate([
            'cif' => 'required|string|max:50',
            'address' => 'required|string|max:255',
            'sector' => 'required|string|max:100',
            'website' => 'nullable|url|max:255',
        ], $messages);

        $detail->update($request->only([
            'cif',
            'address',
            'sector',
            'website',
        ]));
    }

    Session::forget('google_user');
    Auth::login($user);

    return redirect('/dashboard')->with('message', __('messages.messages.register'));
});

// Translate
Route::group(['prefix' => LaravelLocalization::setLocale()], function () {
    /** ADD ALL LOCALIZED ROUTES INSIDE THIS GROUP **/
    Route::get('/', function () {
        if (auth()->check()) {
            return redirect('/dashboard');
        }
        return view('welcome');
    });

    Route::get('/auth', [AuthController::class, 'show'])->name('auth');
    Route::post('/auth', [AuthController::class, 'show'])->name('auth');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/register', [AuthController::class, 'register'])->name('register');

    Route::get('/forgot-password', [PasswordResetController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLinkEmail'])->name('password.email');

    Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('password.update');

    Route::get('test', function () {
        return View::make('test');
    });


    Route::middleware(['auth', 'session.timeout'])->group(function () {
        Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');

        Route::post('/admin/crear-usuario', [AdminController::class, 'userRegister'])->name('admin.register');
        Route::post('/admin/crear-proyecto', [AdminController::class, 'createProject'])->name('admin.create.project');
        Route::post('/admin/crear-proyecto-escolar', [AdminController::class, 'createSchoolProject'])->name('admin.create.school.project');
        Route::post('/admin/crear-oferta', [AdminController::class, 'createJobOffer'])->name('admin.job.offers.store');

        Route::get('/admin/projectos', [AdminController::class, 'projectsShow'])->name('admin.projects');
        Route::get('/admin/proyectos/{id}', [AdminController::class, 'detailsProject'])->name('admin.project.details');
        Route::put('/admin/proyectos/{id}', [AdminController::class, 'detailsProject'])->name('admin.project.details');
        Route::get('/admin/proyectos/{id}/editar', [AdminController::class, 'updateProject'])->name('admin.project.update');
        Route::put('/admin/proyectos/{id}/editar', [AdminController::class, 'updateProject'])->name('admin.project.update');
        Route::delete('/admin/proyectos/{id}', [AdminController::class, 'destroyProject'])->name('admin.project.destroy');

        Route::post('/profesor/importar-alumnos', [AdminController::class, 'importStudents'])->name('professor.import.students');
        Route::post('/profesor/reset-passwords', [AdminController::class, 'resetPasswords'])->name('professor.reset.passwords');



        Route::get('/admin/proyectos-escolares', [AdminController::class, 'showSchoolProjects'])->name('admin.school_projects');
        Route::get('/admin/proyectos-escolares/{id}/detalles', [AdminController::class, 'detailsSchoolProject'])->name('admin.school_project.details');
        Route::put('/admin/proyectos-escolares/{id}/editar', [AdminController::class, 'updateSchoolProject'])->name('admin.school_project.update');
        Route::get('/admin/proyectos-escolares/{id}/editar', [AdminController::class, 'updateSchoolProject'])->name('admin.school_project.update');
        Route::delete('/admin/proyectos-escolares/{id}/eliminar', [AdminController::class, 'destroySchoolProject'])->name('admin.school_project.destroy');


        Route::get('/admin/usuarios', [AdminController::class, 'showUsers'])->name('admin.users');
        Route::put('/admin/usuarios/editar/{id}', [AdminController::class, 'updateUser'])->name('admin.user.update');
        Route::delete('/admin/users/{id}', [AdminController::class, 'destroyUser'])->name('admin.user.destroy');

        Route::get('/admin/comentarios', [AdminController::class, 'showComments'])->name('admin.comments');
        Route::put('/admin/comentarios/{id}/editar', [AdminController::class, 'updateComment'])->name('admin.comments.update');
        Route::delete('/admin/comentarios/{id}/eliminar', [AdminController::class, 'destroyComment'])->name('admin.comment.destroy');

        Route::get('/admin/ofertas', [AdminController::class, 'showOffers'])->name('admin.offers');
        Route::put('/admin/ofertas/{id}', [AdminController::class, 'updateOffer'])->name('admin.offers.update');
        Route::delete('/admin/ofertas/{id}', [AdminController::class, 'destroyOffer'])->name('admin.offers.destroy');

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/perfil', [DashboardController::class, 'profile'])->name('profile.index');
        Route::get('/perfil/{id}', [DashboardController::class, 'publicProfile'])->name('profile.public');

        Route::get('/gestion-proyectos-escolares', [SchoolProjectController::class, 'index'])->name('school.projects.index');
        Route::put('/school-projects/editar/{id}', [SchoolProjectController::class, 'update'])->name('school.projects.update');
        Route::delete('/school-projects/eliminar/{id}', [SchoolProjectController::class, 'destroy'])->name('school.projects.destroy');
        Route::get('/proyectos-escolares/{id}', [SchoolProjectController::class, 'show'])->name('school.projects.show');
        Route::post('/school-projects/creear', [SchoolProjectController::class, 'store'])->name('school.projects.store');


        Route::get('/proyectos', [ProjectController::class, 'index'])->name('projects.index');
        Route::post('/proyectos', [ProjectController::class, 'store'])->name('projects.store');
        Route::get('/proyectos/{id}', [ProjectController::class, 'show'])->name('projects.show');
        Route::get('/tus-proyectos', [ProjectController::class, 'ownProjects'])->name('projects.ownProjects');
        Route::delete('/tus-proyectos/{id}', [ProjectController::class, 'destroy'])->name('projects.destroy');
        Route::put('/tus-proyectos/{id}', [ProjectController::class, 'update'])->name('projects.update');
        Route::delete('/imagen-proyecto/{project}', [ProjectController::class, 'deleteMainImage'])->name('projects.deleteMainImage');


        Route::put('/perfil/{id}', [UserController::class, 'update'])->name('user.update');
        Route::post('/logout', [AuthController::class, 'logout'])->name('user.logout');
        Route::get('/logout', [AuthController::class, 'logout'])->name('user.logout');


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



        Route::get('/notificaciones/check', [NotificationController::class, 'check'])->middleware('auth');

    });

});
