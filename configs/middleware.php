<?php

declare(strict_types = 1);

use App\Config;
use App\Middleware\CsrfFieldsMiddleware;
use App\Middleware\MethodOverrideMiddleware;
use App\Middleware\OldFormDataMiddleware;
use App\Middleware\StartSessionMiddleware;
use App\Middleware\ValidationErrorsMiddleware;
use App\Middleware\ValidationExceptionMiddleware;
use Slim\App;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

return function (App $app) {
    $container = $app->getContainer();
    $config    = $container->get(Config::class);

    $app->add(MethodOverrideMiddleware::class);
    $app->add(CsrfFieldsMiddleware::class);
    $app->add('csrf');
    // Twig
    $app->add(TwigMiddleware::create($app, $container->get(Twig::class)));
    // Form Validations
    $app->add(ValidationExceptionMiddleware::class);

    $app->add(OldFormDataMiddleware::class);
    $app->add(ValidationErrorsMiddleware::class);
    $app->add(StartSessionMiddleware::class);

    // Logger
    $app->addErrorMiddleware(
        (bool) $config->get('display_error_details'),
        (bool) $config->get('log_errors'),
        (bool) $config->get('log_error_details')
    );
};
