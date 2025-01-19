<?php

declare(strict_types = 1);

namespace App\Requests;

use App\Contracts\RequestValidatorInterface;
use App\Exception\ValidationException;
use Valitron\Validator;

class CreateCategoryRequest implements RequestValidatorInterface
{

    public function __construct()
    {
    }

    public function validate(array $data): array
    {
        $validator  = new Validator($data);
        $validator->rule('required', 'name');
        $validator->rule('lengthMax', 'name', 50);
        if(!$validator->validate()) {
            throw new ValidationException($validator->errors());
        }

        return $data;
    }
}
