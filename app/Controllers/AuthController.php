<?php

declare(strict_types = 1);

namespace App\Controllers;

use App\Contracts\AuthInterface;
use App\DataObjects\RegisterUserData;
use App\Entity\User;
use App\Exception\ValidationException;
use Doctrine\ORM\EntityManager;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;
use Valitron\Validator;

class AuthController
{
    public function __construct(
        private readonly Twig $twig,
        private readonly EntityManager $entityManager,
        private readonly AuthInterface $auth,
    )
    {
    }

    public function loginView(Request $request, Response $response) :Response
    {
        return $this->twig->render($response, 'auth/login.twig');
    }

    public function registerView(Request $request, Response $response) :Response
    {
        return $this->twig->render($response, 'auth/register.twig');
    }

    public function register(Request $request, Response $response) :Response
    {
        $data = $request->getParsedBody();

        $validator  = new Validator($data);
        $validator->rule('required', ['name', 'email', 'password', 'confirmPassword']);
        $validator->rule('email', 'email');
        $validator->rule('equals', 'confirmPassword', 'password')->label('Confirm password');
        $validator->rule(
            fn($field, $value, $params, $fields) => ! $this->entityManager->getRepository(User::class)->count(
                ['email' => $value]
            ),
            'email'
        )->message('user already exists');
        if(!$validator->validate()) {
            throw new ValidationException($validator->errors());
        }

        $this->auth->register(
            new RegisterUserData($data['name'], $data['email'], $data['password'])
        );

        return $response->withHeader('Location', '/')->withStatus(302);
    }

    public function login(Request $request, Response $response) :Response
    {
        $data = $request->getParsedBody();

        $validator  = new Validator($data);
        $validator->rule('required', ['email', 'password']);
        $validator->rule('email', 'email');

        if(! $this->auth->attempt($data)) {
            throw new ValidationException(['password' => ['You entered an invalid username or password']]);
        }

        return $response->withHeader('Location', '/')->withStatus(302);
    }

    public function logout(Request $request, Response $response) :Response
    {
        $this->auth->logout();
        return $response->withHeader('Location', '/login')->withStatus(302);
    }
}
