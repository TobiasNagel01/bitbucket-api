<?php

declare(strict_types=1);

namespace Tobiasn\BitbucketApi\Requests\Repositories;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Tobiasn\BitbucketApi\DataTransferObjects\BranchDTO;

/**
 * @see https://developer.atlassian.com/cloud/bitbucket/rest/api-group-refs/#api-repositories-workspace-repo-slug-refs-branches-name-get
 */
final class GetBranchRequest extends Request
{
    protected Method $method = Method::GET;

    /**
     * @param  string  $workspace  This can either be the workspace ID (slug) or the workspace UUID surrounded by curly-braces, for example: {workspace UUID}.
     * @param  string  $repoSlug  This can either be the repository slug or the UUID of the repository, surrounded by curly-braces, for example: {repository UUID}.
     * @param  string  $name  Name of the branch, for example: `feature/login`.
     */
    public function __construct(
        private readonly string $workspace,
        private readonly string $repoSlug,
        private readonly string $name,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/repositories/'.$this->workspace.'/'.$this->repoSlug.'/refs/branches/'.rawurlencode($this->name);
    }

    public function createDtoFromResponse(Response $response): BranchDTO
    {
        return BranchDTO::from($response->json());
    }
}
