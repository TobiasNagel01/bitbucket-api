<?php

declare(strict_types=1);

namespace Tobiasn\BitbucketApi\Requests\Workspaces;

use Illuminate\Support\Collection;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\PaginationPlugin\Contracts\Paginatable;
use Tobiasn\BitbucketApi\DataTransferObjects\WorkspaceAccessDTO;
use Tobiasn\BitbucketApi\Requests\Concerns\CreatesCollectionDto;

/**
 * Lists the workspaces accessible to the authenticated user.
 *
 * Uses `GET /2.0/user/workspaces`. The former `GET /2.0/workspaces` endpoint was
 * removed by Atlassian in 2026 (CHANGE-2770, cross-workspace API sunset).
 *
 * @see https://developer.atlassian.com/cloud/bitbucket/rest/api-group-workspaces/#api-user-workspaces-get
 */
final class ListWorkspacesRequest extends Request implements Paginatable
{
    use CreatesCollectionDto;

    protected Method $method = Method::GET;

    /**
     * @param  bool|null  $administrator  Only workspaces the user administers (`true`) or does not administer (`false`).
     * @param  string|null  $q  Bitbucket query string, for example: `slug="my-workspace"`.
     * @param  string|null  $sort  Field to sort by; only `slug` is supported, prefix with `-` for descending.
     * @param  int|null  $pagelen  Page size.
     */
    public function __construct(
        private readonly ?bool $administrator = null,
        private readonly ?string $q = null,
        private readonly ?string $sort = null,
        private readonly ?int $pagelen = null,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/user/workspaces';
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaultQuery(): array
    {
        return array_filter([
            'administrator' => $this->administrator === null ? null : ($this->administrator ? 'true' : 'false'),
            'q' => $this->q,
            'sort' => $this->sort,
            'pagelen' => $this->pagelen,
        ], fn (mixed $value) => $value !== null);
    }

    /**
     * @return Collection<int, WorkspaceAccessDTO>
     */
    public function createDtoFromResponse(Response $response): Collection
    {
        return $this->mapValues($response, WorkspaceAccessDTO::class);
    }
}
