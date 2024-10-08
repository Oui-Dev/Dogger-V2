<?php

use App\Http\Controllers\StaticViewController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProjectsController;
use App\Http\Controllers\ErrorsController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Public routes
require __DIR__ . '/auth.php';
Route::get('/', [StaticViewController::class, 'homepage'])->name('homepage');

// Authenticated routes
Route::group([
    'prefix' => 'dashboard',
    'as' => 'dashboard.',
    'middleware' => ['auth', 'verified', 'role:user|admin'],
], function () {
    Route::get('/', [DashboardController::class, 'index'])->name('index');

    // Projects
    Route::group([
        'prefix' => 'projects',
        'as' => 'projects.',
        'controller' => ProjectsController::class
    ], function () {
        Route::get('/', 'list')->name('list');
        Route::post('/new', 'create')->name('add');
        Route::put('/edit/{project}', 'update')->name('edit');
        Route::delete('/delete/{project}', 'delete')->name('delete');
    });

    // Errors
    Route::group([
        'prefix' => 'errors',
        'as' => 'errors.',
        'controller' => ErrorsController::class
    ], function () {
        Route::get('/', 'list')->name('list');
        Route::put('/update/{error}', 'update')->name('update');
    });

    // Users
    Route::group([
        'prefix' => 'users',
        'as' => 'users.',
        'controller' => UsersController::class,
        'middleware' => ['role:admin'],
    ], function () {
        Route::get('/', 'list')->name('list');
        Route::post('/new', 'create')->name('add');
        Route::delete('/delete/{user}', 'delete')->name('delete');
    });

    // Profile
    Route::group([
        'prefix' => 'profile',
        'as' => 'profile.',
        'controller' => ProfileController::class
    ], function () {
        Route::get('/', 'show')->name('show');
        Route::put('/edit', 'update')->name('edit');
        Route::delete('/delete', 'delete')->name('delete');
        Route::put('/organization/edit', 'updateOrganization')
            ->middleware('role:admin')
            ->name('organization.edit');
    });
});