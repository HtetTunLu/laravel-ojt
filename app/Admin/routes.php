<?php

use App\Admin\Controllers\PostController;
use Encore\Admin\Controllers\AdminController;
use Illuminate\Routing\Router;

Admin::routes();

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

});
