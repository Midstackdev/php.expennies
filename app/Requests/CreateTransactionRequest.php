<?php

declare(strict_types = 1);

namespace App\Requests;

use App\Contracts\RequestValidatorInterface;
use App\Exception\ValidationException;
use App\Services\CategoryService;
use Valitron\Validator;

class CreateTransactionRequest implements RequestValidatorInterface
{
    public function __construct(
        private readonly CategoryService $categoryService
    )
    {
    }

    public function validate(array $data): array
    {
        $validator = new Validator($data);

        $validator->rule('required', ['description', 'amount', 'date', 'category']);
        $validator->rule('lengthMax', 'description', 255);
        $validator->rule('dateFormat', 'dateFormat', 'm/d/Y g:i A');
        $validator->rule('numeric', 'amount');
        $validator->rule('integer', 'category');
        $validator->rule(
            function($field, $value, $params, $fields) use (&$data) {
                $id = (int) $value;

                if (! $id) {
                    return false;
                }

                $category = $this->categoryService->findById($id);

                if ($category) {
                    $data['category'] = $category;

                    return true;
                }

                return false;
            },
            'category'
        )->message('Category not found');

        if (! $validator->validate()) {
            throw new ValidationException($validator->errors());
        }

        return $data;
    }
}
