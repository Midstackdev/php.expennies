<?php

declare(strict_types = 1);

namespace App\Controllers;

use App\Contracts\RequestValidatorFactoryInterface;
use App\DataObjects\TransactionData;
use App\Entity\Transaction;
use App\Requests\CreateTransactionRequest;
use App\ResponseFormatter;
use App\Services\CategoryService;
use App\Services\RequestService;
use App\Services\TransactionService;
use DateTime;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;

class TransactionsController
{
    public function __construct(
        private readonly Twig $twig,
        private readonly ResponseFormatter $responseFormatter,
        private readonly RequestService $requestService,
        private readonly TransactionService $transactionService,
        private readonly CategoryService $categoryService,
        private readonly RequestValidatorFactoryInterface $requestValidatorFactory,
    )
    {
    }

    public function index(Request $request, Response $response) :Response
    {
        return $this->twig->render(
            $response,
            'transactions/index.twig',
            ['categories' => $this->categoryService->getCategoryNames()]
        );
    }

    public function store(Request $request, Response $response): Response
    {
        $data = $this->requestValidatorFactory->make(CreateTransactionRequest::class)->validate(
            $request->getParsedBody()
        );

        $this->transactionService->create(
            new TransactionData(
                $data['description'],
                (float) $data['amount'],
                new DateTime($data['date']),
                $data['category']
            ),
            $request->getAttribute('user')
        );

        return $response;
    }

    public function get(Request $request, Response $response, array $args): Response
    {
        $transaction = $this->transactionService->findById((int) $args['id']);

        if (! $transaction) {
            return $response->withStatus(404);
        }

        $data = [
            'id'          => $transaction->getId(),
            'description' => $transaction->getDescription(),
            'amount'      => $transaction->getAmount(),
            'date'        => $transaction->getDate()->format('Y-m-d\TH:i'),
            'category'    => $transaction->getCategory()->getId(),
        ];

        return $this->responseFormatter->asJson($response, $data);
    }

    public function update(Request $request, Response $response, array $args): Response
    {
        $data = $this->requestValidatorFactory->make(CreateTransactionRequest::class)->validate(
            $args + $request->getParsedBody()
        );

        $id = (int) $data['id'];

        if (! $id || ! ($transaction = $this->transactionService->findById($id))) {
            return $response->withStatus(404);
        }

        $this->transactionService->update(
            $transaction,
            new TransactionData(
                $data['description'],
                (float) $data['amount'],
                new DateTime($data['date']),
                $data['category']
            )
        );

        return $response;
    }

    public function delete(Request $request, Response $response, array $args): Response
    {
        $this->transactionService->delete((int) $args['id']);

        return $response;
    }

    public function load(Request $request, Response $response) :Response
    {
        $params       = $this->requestService->getDataTableQueryParams($request);
        $transactions = $this->transactionService->getPaginatedTransactions($params);
        $transformer  = function (Transaction $transaction) {
            return [
                'id'          => $transaction->getId(),
                'description' => $transaction->getDescription(),
                'amount'      => $transaction->getAmount(),
                'date'        => $transaction->getDate()->format('m/d/Y g:i A'),
                'category'    => $transaction->getCategory()->getName(),
            ];
        };

        $totalTransactions = count($transactions);

        return $this->responseFormatter->asJson($response, [
            'data' => array_map($transformer, (array) $transactions->getIterator()),
            'draw' => $params->draw,
            'total' => $totalTransactions,
            'filtered' => $totalTransactions,
        ]);
    }
}
