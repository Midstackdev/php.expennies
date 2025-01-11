<?php

declare(strict_types = 1);

namespace App\Middleware;

use App\Exception\ValidationException;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class AuthMiddleware implements MiddlewareInterface
{

    public function __construct(private readonly ResponseFactoryInterface $responseFactory)
    {
    }

    public function process(Request $request, RequestHandler $handler): Response
    {
        if(empty($_SESSION['user'])) {
            $response = $this->responseFactory->createResponse();
            return $response->withHeader('Location', '/login')->withStatus(302);
        }
        return $handler->handle($request);
    }
}
