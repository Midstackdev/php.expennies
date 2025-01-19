<?php

declare(strict_types = 1);

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class MethodOverrideMiddleware implements MiddlewareInterface
{

    public function process(Request $request, RequestHandler $handler): Response
    {
        $methodHeader = $request->getHeaderLine('X-Http-Method-Override');
        if($methodHeader) {
            $request = $request->withMethod($methodHeader);
        }

        if(strtoupper($request->getMethod()) === 'POST') {
            $body = $request->getParsedBody();

            if(is_array($body) && !empty($body['_METHOD'])) {
                $request = $request->withMethod($body['_METHOD']);
            }

            if($request->getBody()->eof()) {
                $request->getBody()->rewind();
            }
        }

        return $handler->handle($request);
    }
}
