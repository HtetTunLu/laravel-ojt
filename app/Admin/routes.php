<?php

use App\Admin\Controllers\PostController;
use App\Admin\Controllers\RegisterController;
use Encore\Admin\Controllers\AdminController;
use Illuminate\Routing\Router;

Encore\Admin\Facades\Admin::routes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
    'as'            => config('admin.route.prefix') . '.',
], function (Router $router) {
    // default
    $router->get('/', 'HomeController@index')->name('home');
    $router->resource('users-clients', UserController::class);
    $router->resource('posts', PostController::class);

    // for custom confirm edit
    $router->post('/posts/{id}/confirm_update', [AdminController::class, 'confirm_update']);
    $router->post('/users-clients/{id}/confirm_update', [AdminController::class, 'confirm_update']);
    $router->put('/posts/{id}/confirm_update', [AdminController::class, 'edit']);
    $router->put('/users-clients/{id}/confirm_update', [AdminController::class, 'edit']);

    // for CSV import
    $router->post('questions/csv/import', 'PostController@import');

    $router->get('auth/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    $router->post('auth/register', [RegisterController::class, 'register']);

    $router->get('auth/signin', [RegisterController::class, 'showSigninForm'])->name('signin');
    $router->get('auth/forgot', [RegisterController::class, 'showForgotForm']);
    $router->post('auth/forgot', [RegisterController::class, 'forgotPassword']);

    $router->get('auth/reset', [RegisterController::class, 'showResetForm']);
    $router->post('auth/reset', [RegisterController::class, 'resetPassword']);
});
