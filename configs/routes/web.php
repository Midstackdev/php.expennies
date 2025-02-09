<?php

declare(strict_types = 1);

use App\Controllers\AuthController;
use App\Controllers\CategoriesController;
use App\Controllers\HomeController;
use App\Middleware\AuthMiddleware;
use App\Middleware\GuestMiddleware;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

return function (App $app) {
    $app->get('/', [HomeController::class, 'index'])->add(AuthMiddleware::class);

    $app->group('', function(RouteCollectorProxy $group) {
        $group->get('/login', [AuthController::class, 'loginView']);
        $group->get('/register', [AuthController::class, 'registerView']);
        $group->post('/login', [AuthController::class, 'login']);
        $group->post('/register', [AuthController::class, 'register']);
    })->add(GuestMiddleware::class);

    $app->post('/logout', [AuthController::class, 'logout'])->add(AuthMiddleware::class);

    $app->group('/categories', function(RouteCollectorProxy $category) {
        $category->get('', [CategoriesController::class, 'index']);
        $category->get('/load', [CategoriesController::class, 'load']);
        $category->post('', [CategoriesController::class, 'store']);
        $category->delete('/{id:[0-9]+}', [CategoriesController::class, 'delete']);
        $category->get('/{id:[0-9]+}', [CategoriesController::class, 'get']);
        $category->post('/{id}', [CategoriesController::class, 'update']);
    })->add(AuthMiddleware::class);
};
