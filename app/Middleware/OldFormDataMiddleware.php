<?php

declare(strict_types = 1);

namespace App\Middleware;

use App\Contracts\SessionInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Views\Twig;

class OldFormDataMiddleware implements MiddlewareInterface
{

    public function __construct(
        private readonly Twig $twig,
        private readonly SessionInterface $session,
    ){
    }


    public function process(Request $request, RequestHandler $handler): Response
    {
        if($old = $this->session->getFlash('old')) {
            $this->twig->getEnvironment()->addGlobal('old', $old);
        }
        $response = $handler->handle($request);

        return $response;

    }
}
