<?php

declare(strict_types=1);

namespace Tobiasn\BitbucketApi\Requests\Repositories;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Tobiasn\BitbucketApi\DataTransferObjects\RepositoryDTO;

/**
 * @see https://developer.atlassian.com/cloud/bitbucket/rest/api-group-repositories/#api-repositories-workspace-repo-slug-get
 */
class GetRepositoryRequest extends Request
{
    protected Method $method = Method::GET;

    /**
     * @param  string  $workspace  This can either be the workspace ID (slug) or the workspace UUID surrounded by curly-braces, for example: {workspace UUID}.
     * @param  string  $repoSlug  This can either be the repository slug or the UUID of the repository, surrounded by curly-braces, for example: {repository UUID}.
     */
    public function __construct(
        private readonly string $workspace,
        private readonly string $repoSlug,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/repositories/'.$this->workspace.'/'.$this->repoSlug;
    }

    public function createDtoFromResponse(Response $response): RepositoryDTO
    {
        return RepositoryDTO::from($response->json());
    }
}
