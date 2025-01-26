<?php

declare(strict_types = 1);

namespace App\Requests;

use App\Contracts\RequestValidatorInterface;
use App\Exception\ValidationException;
use Valitron\Validator;

class UpdateCategoryRequest implements RequestValidatorInterface
{

    public function validate(array $data): array
    {
        $validator  = new Validator($data);
        $validator->rule('required', ['name', 'id']);
        $validator->rule('lengthMax', 'name', 50);
        $validator->rule('integer', 'id');
        if(!$validator->validate()) {
            throw new ValidationException($validator->errors());
        }

        return $data;
    }
}
