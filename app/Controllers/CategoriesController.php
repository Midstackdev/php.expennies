<?php

namespace App\Controllers;

declare(strict_types = 1);

use App\Contracts\RequestValidatorFactoryInterface;
use App\Requests\CreateCategoryRequest;
use App\Requests\UpdateCategoryRequest;
use App\ResponseFormatter;
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
        private readonly ResponseFormatter $responseFormatter,
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


    public function get(Request $request, Response $response, array $args) :Response
    {
        $category = $this->categoryService->findById((int) $args['id']);

        if(! $category) {
            return $response->withStatus(404);
        }

        $data = ['name' => $category->getName(), 'id' => $category->getId()];

        return $this->responseFormatter->asJson($response, $data);
    }

    public function update(Request $request, Response $response, array $args) :Response
    {
        $data = $this->requestValidatorFactory->make(UpdateCategoryRequest::class)->validate($request->getParsedBody());
        $category = $this->categoryService->findById((int) $args['id']);

        if(! $category) {
            return $response->withStatus(404);
        }

        $data = ['status' => 'ok'];

        return $this->responseFormatter->asJson($response, $data);
    }

}
