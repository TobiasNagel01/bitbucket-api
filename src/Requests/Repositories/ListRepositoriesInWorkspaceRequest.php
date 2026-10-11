<?php

declare(strict_types=1);

namespace Tobiasn\BitbucketApi\Requests\Repositories;

use Illuminate\Support\Collection;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\PaginationPlugin\Contracts\Paginatable;
use Tobiasn\BitbucketApi\DataTransferObjects\RepositoryDTO;
use Tobiasn\BitbucketApi\Requests\Concerns\CreatesCollectionDto;

/**
 * @see https://developer.atlassian.com/cloud/bitbucket/rest/api-group-repositories/#api-repositories-workspace-get
 */
final class ListRepositoriesInWorkspaceRequest extends Request implements Paginatable
{
    use CreatesCollectionDto;

    protected Method $method = Method::GET;

    /**
     * @param  string  $workspace  This can either be the workspace ID (slug) or the workspace UUID surrounded by curly-braces, for example: {workspace UUID}.
     * @param  string|null  $role  Filter by the authenticated user's role: `member`, `contributor`, `admin` or `owner`.
     * @param  string|null  $q  Bitbucket query string, for example: `name ~ "api"`.
     * @param  string|null  $sort  Field to sort by, prefix with `-` for descending, for example: `-updated_on`.
     * @param  int|null  $pagelen  Page size.
     */
    public function __construct(
        private readonly string $workspace,
        private readonly ?string $role = null,
        private readonly ?string $q = null,
        private readonly ?string $sort = null,
        private readonly ?int $pagelen = null,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/repositories/'.$this->workspace;
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
     * @return Collection<int, RepositoryDTO>
     */
    public function createDtoFromResponse(Response $response): Collection
    {
        return $this->mapValues($response, RepositoryDTO::class);
    }
}
