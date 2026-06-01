<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Frontend\CertificateController;
use App\Http\Controllers\Frontend\CourseController;
use App\Http\Controllers\Frontend\DashboardController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\InteractionController;
use App\Http\Controllers\Frontend\LearnController;
use App\Http\Controllers\Frontend\ProfileController;
use App\Http\Controllers\Frontend\QuizController;
use App\Http\Controllers\LocaleController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public routes
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');

// Switch interface language
Route::get('/locale/{locale}', [LocaleController::class, 'switch'])->name('locale.switch');

Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
Route::get('/courses/{course}', [CourseController::class, 'show'])->name('courses.show');

// Knowledge-check tests catalog + public preview of a single test
Route::get('/tests', [QuizController::class, 'index'])->name('quizzes.index');

// Public certificate verification (no auth)
Route::get('/verify', [CertificateController::class, 'verify'])->name('certificates.verify');

/*
|--------------------------------------------------------------------------
| Guest auth routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);

    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')->name('logout');

/*
|--------------------------------------------------------------------------
| Authenticated user routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    // Personal dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // Enrollment + learning player
    Route::post('/courses/{course}/enroll', [LearnController::class, 'enroll'])->name('enroll');
    Route::get('/learn/{course}/{lesson}', [LearnController::class, 'lesson'])->name('learn.lesson');
    Route::post('/learn/{course}/{lesson}/complete', [LearnController::class, 'complete'])->name('learn.complete');

    // Likes + comments
    Route::post('/courses/{course}/like', [InteractionController::class, 'toggleLike'])->name('like.toggle');
    Route::post('/courses/{course}/comments', [InteractionController::class, 'comment'])->name('comment.store');
    Route::delete('/comments/{comment}', [InteractionController::class, 'deleteComment'])->name('comment.delete');

    // Knowledge tests (attempting requires auth)
    Route::get('/tests/{quiz}', [QuizController::class, 'show'])->name('quizzes.show');
    Route::post('/tests/{quiz}/submit', [QuizController::class, 'submit'])->name('quizzes.submit');
    Route::get('/results/{attempt}', [QuizController::class, 'result'])->name('quizzes.result');

    // Certificates (owner only)
    Route::get('/certificates/{certificate}', [CertificateController::class, 'show'])->name('certificates.show');
    Route::get('/certificates/{certificate}/download', [CertificateController::class, 'download'])->name('certificates.download');
});

/*
|--------------------------------------------------------------------------
| Admin panel
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

        // Categories (unlimited nesting)
        Route::get('/categories', [\App\Http\Controllers\Admin\CategoryController::class, 'index'])->name('categories.index');
        Route::post('/categories', [\App\Http\Controllers\Admin\CategoryController::class, 'store'])->name('categories.store');
        Route::put('/categories/{category}', [\App\Http\Controllers\Admin\CategoryController::class, 'update'])->name('categories.update');
        Route::post('/categories/reorder', [\App\Http\Controllers\Admin\CategoryController::class, 'reorder'])->name('categories.reorder');
        Route::delete('/categories/{category}', [\App\Http\Controllers\Admin\CategoryController::class, 'destroy'])->name('categories.destroy');

        // Courses
        Route::get('/courses', [\App\Http\Controllers\Admin\CourseController::class, 'index'])->name('courses.index');
        Route::get('/courses/create', [\App\Http\Controllers\Admin\CourseController::class, 'create'])->name('courses.create');
        Route::post('/courses', [\App\Http\Controllers\Admin\CourseController::class, 'store'])->name('courses.store');
        Route::get('/courses/{course}/edit', [\App\Http\Controllers\Admin\CourseController::class, 'edit'])->name('courses.edit');
        Route::put('/courses/{course}', [\App\Http\Controllers\Admin\CourseController::class, 'update'])->name('courses.update');
        Route::delete('/courses/{course}', [\App\Http\Controllers\Admin\CourseController::class, 'destroy'])->name('courses.destroy');
        Route::post('/courses/{course}/toggle', [\App\Http\Controllers\Admin\CourseController::class, 'togglePublish'])->name('courses.toggle');

        // Sections
        Route::post('/courses/{course}/sections', [\App\Http\Controllers\Admin\SectionController::class, 'store'])->name('sections.store');
        Route::put('/sections/{section}', [\App\Http\Controllers\Admin\SectionController::class, 'update'])->name('sections.update');
        Route::post('/courses/{course}/sections/reorder', [\App\Http\Controllers\Admin\SectionController::class, 'reorder'])->name('sections.reorder');
        Route::delete('/sections/{section}', [\App\Http\Controllers\Admin\SectionController::class, 'destroy'])->name('sections.destroy');

        // Lessons
        Route::post('/sections/{section}/lessons', [\App\Http\Controllers\Admin\LessonController::class, 'store'])->name('lessons.store');
        Route::put('/lessons/{lesson}', [\App\Http\Controllers\Admin\LessonController::class, 'update'])->name('lessons.update');
        Route::post('/sections/{section}/lessons/reorder', [\App\Http\Controllers\Admin\LessonController::class, 'reorder'])->name('lessons.reorder');
        Route::delete('/lessons/{lesson}', [\App\Http\Controllers\Admin\LessonController::class, 'destroy'])->name('lessons.destroy');

        // Materials (documents / videos attached to courses & lessons)
        Route::post('/courses/{course}/materials', [\App\Http\Controllers\Admin\MaterialController::class, 'store'])->name('materials.store');
        Route::delete('/materials/{material}', [\App\Http\Controllers\Admin\MaterialController::class, 'destroy'])->name('materials.destroy');

        // Quizzes / tests
        Route::get('/quizzes', [\App\Http\Controllers\Admin\QuizController::class, 'index'])->name('quizzes.index');
        Route::get('/quizzes/create', [\App\Http\Controllers\Admin\QuizController::class, 'create'])->name('quizzes.create');
        Route::post('/quizzes', [\App\Http\Controllers\Admin\QuizController::class, 'store'])->name('quizzes.store');
        Route::get('/quizzes/{quiz}/edit', [\App\Http\Controllers\Admin\QuizController::class, 'edit'])->name('quizzes.edit');
        Route::put('/quizzes/{quiz}', [\App\Http\Controllers\Admin\QuizController::class, 'update'])->name('quizzes.update');
        Route::delete('/quizzes/{quiz}', [\App\Http\Controllers\Admin\QuizController::class, 'destroy'])->name('quizzes.destroy');
        Route::post('/quizzes/{quiz}/questions', [\App\Http\Controllers\Admin\QuizController::class, 'storeQuestion'])->name('questions.store');
        Route::delete('/questions/{question}', [\App\Http\Controllers\Admin\QuizController::class, 'destroyQuestion'])->name('questions.destroy');

        // Comments moderation
        Route::get('/comments', [\App\Http\Controllers\Admin\CommentController::class, 'index'])->name('comments.index');
        Route::post('/comments/{comment}/reply', [\App\Http\Controllers\Admin\CommentController::class, 'reply'])->name('comments.reply');
        Route::post('/comments/{comment}/toggle', [\App\Http\Controllers\Admin\CommentController::class, 'toggleHide'])->name('comments.toggle');
        Route::delete('/comments/{comment}', [\App\Http\Controllers\Admin\CommentController::class, 'destroy'])->name('comments.destroy');

        // Users
        Route::get('/users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
        Route::get('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'show'])->name('users.show');
        Route::post('/users/{user}/block', [\App\Http\Controllers\Admin\UserController::class, 'block'])->name('users.block');
        Route::post('/users/{user}/unblock', [\App\Http\Controllers\Admin\UserController::class, 'unblock'])->name('users.unblock');
        Route::post('/users/{user}/role', [\App\Http\Controllers\Admin\UserController::class, 'toggleRole'])->name('users.role');

        // Menus
        Route::get('/menus', [\App\Http\Controllers\Admin\MenuController::class, 'index'])->name('menus.index');
        Route::post('/menus/{menu}/items', [\App\Http\Controllers\Admin\MenuController::class, 'storeItem'])->name('menus.items.store');
        Route::put('/menu-items/{item}', [\App\Http\Controllers\Admin\MenuController::class, 'updateItem'])->name('menus.items.update');
        Route::delete('/menu-items/{item}', [\App\Http\Controllers\Admin\MenuController::class, 'destroyItem'])->name('menus.items.destroy');

        // Settings
        Route::get('/settings', [\App\Http\Controllers\Admin\SettingController::class, 'edit'])->name('settings.edit');
        Route::put('/settings', [\App\Http\Controllers\Admin\SettingController::class, 'update'])->name('settings.update');
    });
