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

    $app->group('/categories', function(RouteCollectorProxy $group) {
        $group->get('', [CategoriesController::class, 'index']);
        $group->post('', [CategoriesController::class, 'store']);
        $group->delete('/{id}', [CategoriesController::class, 'delete']);
        $group->get('/{id}', [CategoriesController::class, 'get']);
        $group->post('/{id}', [CategoriesController::class, 'update']);
    })->add(AuthMiddleware::class);
};
