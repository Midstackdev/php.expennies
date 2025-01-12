<?php

declare(strict_types = 1);

namespace App\Middleware;

use App\Contracts\SessionInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class StartSessionMiddleware implements MiddlewareInterface
{

    public function __construct(private readonly SessionInterface $session)
    {
    }


    public function process(Request $request, RequestHandler $handler): Response
    {

        $this->session->start();

        $response = $handler->handle($request);
        session_write_close();

        return $response;

    }
}
