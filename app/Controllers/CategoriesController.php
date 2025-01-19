<?php

declare(strict_types = 1);

namespace App\Controllers;

use App\Contracts\RequestValidatorFactoryInterface;
use App\Requests\CreateCategoryRequest;
use App\Services\CategoryService;
use Slim\Views\Twig;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class CategoriesController
{
    public function __construct(
        private readonly Twig $twig,
        private readonly RequestValidatorFactoryInterface $requestValidatorFactory,
        private readonly CategoryService $categoryService,
    )
    {
    }

    public function index(Request $request, Response $response) :Response
    {
        return $this->twig->render(
            $response,
            'categories/index.twig',
            ['categories' => $this->categoryService->getAll()]
        );
    }

    public function store(Request $request, Response $response) :Response
    {
        $data = $this->requestValidatorFactory->make(CreateCategoryRequest::class)->validate($request->getParsedBody());

        $this->categoryService->create($data['name'], $request->getAttribute('user'));

        return $response->withHeader('Location', '/categories')->withStatus(302);
    }

    public function delete(Request $request, Response $response, array $args) :Response
    {
        $this->categoryService->delete((int) $args['id']);
        return $response->withHeader('Location', '/categories')->withStatus(302);
    }

}
