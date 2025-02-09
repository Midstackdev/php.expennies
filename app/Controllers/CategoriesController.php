<?php

declare(strict_types = 1);

namespace App\Controllers;

use App\Contracts\RequestValidatorFactoryInterface;
use App\Entity\Category;
use App\Requests\CreateCategoryRequest;
use App\Requests\UpdateCategoryRequest;
use App\ResponseFormatter;
use App\Services\CategoryService;
use App\Services\RequestService;
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
        private readonly RequestService $requestService,
    )
    {
    }

    public function index(Request $request, Response $response) :Response
    {
        return $this->twig->render($response, 'categories/index.twig');
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
        return $response;
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
        $data = $this->requestValidatorFactory->make(UpdateCategoryRequest::class)->validate(
            $args + $request->getParsedBody()
        );
        $category = $this->categoryService->findById((int) $data['id']);

        if(! $category) {
            return $response->withStatus(404);
        }

        $this->categoryService->update($category, $data['name']);

        return $this->responseFormatter->asJson($response, $data);
    }

    public function load(Request $request, Response $response) :Response
    {
        $params = $this->requestService->getDataTableQueryParams($request);

        $categories =  $this->categoryService->getPaginatedQuery($params);

        $mapper = function (Category $category) {
            return [
                'id' => $category->getId(),
                'name' => $category->getName(),
                'createdAt' => $category->getCreatedAt()->format('m/d/Y g:i A'),
                'updatedAt' => $category->getUpdatedAt()->format('m/d/Y g:i A')
            ];
        };

        $totalCategories = count($categories);

        return $this->responseFormatter->asJson($response, [
            'data' => array_map($mapper, (array) $categories->getIterator()),
            'draw' => $params->draw,
            'total' => $totalCategories,
            'filtered' => $totalCategories,
        ]);
    }

}
