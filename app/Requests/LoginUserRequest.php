<?php

declare(strict_types = 1);

namespace App\Requests;

use App\Contracts\RequestValidatorInterface;
use App\Exception\ValidationException;
use Doctrine\ORM\EntityManager;
use Valitron\Validator;

class LoginUserRequest implements RequestValidatorInterface
{

    public function __construct(private readonly EntityManager $entityManager)
    {
    }

    public function validate(array $data): array
    {
        $validator  = new Validator($data);
        $validator->rule('required', ['email', 'password']);
        $validator->rule('email', 'email');
        if(!$validator->validate()) {
            throw new ValidationException($validator->errors());
        }

        return $data;
    }
}
