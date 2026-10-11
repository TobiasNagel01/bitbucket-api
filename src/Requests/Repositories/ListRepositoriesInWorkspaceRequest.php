<?php

declare(strict_types=1);

namespace Tobiasn\BitbucketApi\Requests\Repositories;

use Illuminate\Support\Collection;
use RuntimeException;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Tobiasn\BitbucketApi\DataTransferObjects\RepositoryDTO;
use Tobiasn\BitbucketApi\Responses\BitbucketApiResponse;

/**
 * @see https://developer.atlassian.com/cloud/bitbucket/rest/api-group-repositories/#api-repositories-workspace-get
 */
class ListRepositoriesInWorkspaceRequest extends Request
{
    protected Method $method = Method::GET;

    /**
     * @param  string  $workspace  This can either be the workspace ID (slug) or the workspace UUID surrounded by curly-braces, for example: {workspace UUID}.
     */
    public function __construct(
        private readonly string $workspace,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/repositories/'.$this->workspace;
    }

    /**
     * @return Collection<int, RepositoryDTO>
     */
    public function createDtoFromResponse(Response $response): Collection
    {
        if (! $response instanceof BitbucketApiResponse) {
            throw new RuntimeException;
        }

        return collect($response->arr('values'))->map(function ($rep) {
            return RepositoryDTO::from($rep);
        });
    }
}
