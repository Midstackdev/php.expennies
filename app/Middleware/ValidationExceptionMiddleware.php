<?php

namespace App\Middleware;

use App\Exception\ValidationException;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class ValidationExceptionMiddleware implements MiddlewareInterface
{

    public function __construct(private readonly ResponseFactoryInterface $responseFactory)
    {
    }

    public function process(Request $request, RequestHandler $handler): Response
    {
        try {
            return $handler->handle($request);
        } catch (ValidationException $e) {
            $response = $this->responseFactory->createResponse();
            $referer = $request->getServerParams()['HTTP_REFERER'];

            return $response->withHeader('Location', $referer)->withStatus(302);
        }
    }
}
