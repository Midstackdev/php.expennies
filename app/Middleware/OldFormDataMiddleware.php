<?php

declare(strict_types = 1);

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Views\Twig;

class OldFormDataMiddleware implements MiddlewareInterface
{

    public function __construct(private readonly Twig $twig)
    {
    }


    public function process(Request $request, RequestHandler $handler): Response
    {
        if(! empty($_SESSION['old'])) {
            $old = $_SESSION['old'];

            $this->twig->getEnvironment()->addGlobal('old', $old);

            unset($_SESSION['old']);
        }
        $response = $handler->handle($request);

        return $response;

    }
}
