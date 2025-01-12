<?php

declare(strict_types = 1);

namespace App\Middleware;

use App\Contracts\AuthInterface;
use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class AuthenticateMiddleware implements MiddlewareInterface
{

    public function __construct(private readonly AuthInterface $auth)
    {
    }

    public function process(Request $request, RequestHandler $handler): Response
    {
        // if(! empty($_SESSION['user'])) {
        //     $user = $this->entityManager->getRepository(User::class)->find($_SESSION['user']);

        //     $request = $request->withAttribute('user', $user);
        // }
        return $handler->handle($request->withAttribute('user', $this->auth->user()));
    }
}
