<?php

declare(strict_types = 1);

namespace App\Middleware;

use App\Contracts\SessionInterface;
use App\Services\RequestService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class StartSessionMiddleware implements MiddlewareInterface
{

    public function __construct(
        private readonly SessionInterface $session,
        private readonly RequestService $requestService,
    ){
    }


    public function process(Request $request, RequestHandler $handler): Response
    {

        $this->session->start();

        $response = $handler->handle($request);

        if($request->getMethod() === 'GET' && ! $this->requestService->wantsJson($request)) {
            $this->session->put('previousUrl', (string) $request->getUri());
        }

        $this->session->save();

        return $response;

    }
}
