<?php

declare(strict_types=1);

namespace Tobiasn\BitbucketApi\Pagination;

use Illuminate\Support\Collection;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\PaginationPlugin\Paginator;

/**
 * Paginates Bitbucket's `{ values, next, page, pagelen, size }` envelope.
 *
 * Bitbucket returns an absolute `next` URL. Its query string carries whatever the
 * endpoint needs for the next page (`page=N` or an opaque `ctx=` cursor), so the
 * paginator merges that query string into the next request instead of following
 * the absolute URL.
 */
final class BitbucketPaginator extends Paginator
{
    protected function applyPagination(Request $request): Request
    {
        if ($this->currentResponse === null) {
            if ($this->perPageLimit !== null) {
                $request->query()->add('pagelen', $this->perPageLimit);
            }

            return $request;
        }

        $next = $this->currentResponse->json('next');

        if (! is_string($next)) {
            return $request;
        }

        $queryString = parse_url($next, PHP_URL_QUERY);

        if (! is_string($queryString)) {
            return $request;
        }

        parse_str($queryString, $params);

        $query = [];

        foreach ($params as $key => $value) {
            $query[(string) $key] = $value;
        }

        $request->query()->merge($query);

        return $request;
    }

    protected function isLastPage(Response $response): bool
    {
        return ! is_string($response->json('next'));
    }

    /**
     * @return array<int, mixed>
     */
    protected function getPageItems(Response $response, Request $request): array
    {
        $dto = $response->dto();

        return $dto instanceof Collection ? $dto->values()->all() : [];
    }
}
