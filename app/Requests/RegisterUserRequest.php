<?php

declare(strict_types = 1);

namespace App\Requests;

use App\Contracts\RequestValidatorInterface;
use App\Entity\User;
use App\Exception\ValidationException;
use Doctrine\ORM\EntityManager;
use Valitron\Validator;

class RegisterUserRequest implements RequestValidatorInterface
{

    public function __construct(private readonly EntityManager $entityManager)
    {
    }

    public function validate(array $data): array
    {
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

        return $data;
    }
}
