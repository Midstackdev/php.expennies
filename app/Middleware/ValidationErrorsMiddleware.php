<?php

declare(strict_types = 1);

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Views\Twig;

class ValidationErrorsMiddleware implements MiddlewareInterface
{

    public function __construct(private readonly Twig $twig)
    {
    }


    public function process(Request $request, RequestHandler $handler): Response
    {
        if(! empty($_SESSION['errors'])) {
            $errors = $_SESSION['errors'];

            $this->twig->getEnvironment()->addGlobal('errors', $errors);

            unset($_SESSION['errors']);
        }
        $response = $handler->handle($request);

        return $response;

    }
}
