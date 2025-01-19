<?php

declare(strict_types = 1);

namespace App\Middleware;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Views\Twig;

class CsrfFieldsMiddleware implements MiddlewareInterface
{

    public function __construct(
        private readonly Twig $twig,
        private readonly ContainerInterface $container,
    ){
    }


    public function process(Request $request, RequestHandler $handler): Response
    {
        $csrf = $this->container->get('csrf');
        // CSRF token name and value
        $csrfNameKey = $csrf->getTokenNameKey();
        $csrfValueKey = $csrf->getTokenValueKey();
        $csrfName = $csrf->getTokenName();
        $csrfValue = $csrf->getTokenValue();
        $fields = <<<CSRF_Fields
            <input type="hidden" name="$csrfNameKey" value="$csrfName">
            <input type="hidden" name="$csrfValueKey" value="$csrfValue">
        CSRF_Fields;


        $this->twig->getEnvironment()->addGlobal('csrf',
            [
                'keys' => [
                    'name'  => $csrfNameKey,
                    'value' => $csrfValueKey
                ],
                'name'  => $csrfName,
                'value' => $csrfValue,
                'fields' => $fields
            ]
        );

        $response = $handler->handle($request);

        return $response;

    }
}
