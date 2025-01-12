<?php

declare(strict_types = 1);

namespace App\Middleware;

use App\Contracts\SessionInterface;
use App\Exception\ValidationException;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class GuestMiddleware implements MiddlewareInterface
{

    public function __construct(
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly SessionInterface $session,
    ){
    }

    public function process(Request $request, RequestHandler $handler): Response
    {
        if($this->session->get('user')) {
            $response = $this->responseFactory->createResponse();
            return $response->withHeader('Location', '/')->withStatus(302);
        }
        return $handler->handle($request);
    }
}
