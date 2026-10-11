<?php

declare(strict_types=1);

namespace Tobiasn\BitbucketApi\Requests\Workspaces;

use Illuminate\Support\Collection;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\PaginationPlugin\Contracts\Paginatable;
use Tobiasn\BitbucketApi\DataTransferObjects\WorkspaceDTO;
use Tobiasn\BitbucketApi\Requests\Concerns\CreatesCollectionDto;

/**
 * @see https://developer.atlassian.com/cloud/bitbucket/rest/api-group-workspaces/#api-workspaces-get
 */
final class ListWorkspacesRequest extends Request implements Paginatable
{
    use CreatesCollectionDto;

    protected Method $method = Method::GET;

    /**
     * @param  string|null  $role  Filter by the authenticated user's role: `member`, `collaborator` or `owner`.
     * @param  string|null  $q  Bitbucket query string, for example: `slug="my-workspace"`.
     * @param  string|null  $sort  Field to sort by, prefix with `-` for descending, for example: `-created_on`.
     * @param  int|null  $pagelen  Page size.
     */
    public function __construct(
        private readonly ?string $role = null,
        private readonly ?string $q = null,
        private readonly ?string $sort = null,
        private readonly ?int $pagelen = null,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/workspaces';
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaultQuery(): array
    {
        return array_filter([
            'role' => $this->role,
            'q' => $this->q,
            'sort' => $this->sort,
            'pagelen' => $this->pagelen,
        ], fn (mixed $value) => $value !== null);
    }

    /**
     * @return Collection<int, WorkspaceDTO>
     */
    public function createDtoFromResponse(Response $response): Collection
    {
        return $this->mapValues($response, WorkspaceDTO::class);
    }
}
