<?php

declare(strict_types = 1);

namespace App\Services;

use App\Contracts\SessionInterface;
use App\DataObjects\DataTableQueryParams;
use Psr\Http\Message\ServerRequestInterface as Request;

class RequestService
{

    public function __construct(private readonly SessionInterface $session)
    {
    }

    public function getReferer(Request $request)
    {
        $referer = $request->getHeader('referer')[0] ?? '';

        if(! $referer) {
            return $this->session->get('previousUrl');
        }

        $refererHost = parse_url($referer, PHP_URL_HOST);

        if($refererHost !== $request->getUri()->getHost()) {
            $referer = $this->session->get('previousUrl');
        }

        return $referer;
    }

    public function wantsJson(Request $request): bool
    {
        return $request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest';
    }

    public function getDataTableQueryParams(Request $request): DataTableQueryParams
    {
        $params = $request->getQueryParams();
        $orderBy = $params['columns'][$params['order'][0]['column']]['data'];
        $orderDir = $params['order'][0]['dir'];

        return new DataTableQueryParams(
            (int) $params['start'],
            (int) $params['length'],
            $orderBy,
            $orderDir,
            $params['search']['value'],
            (int) $params['draw']
        );
    }

}
