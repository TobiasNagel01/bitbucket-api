<?php

declare(strict_types=1);

namespace Tobiasn\BitbucketApi\Requests\PullRequests;

use Illuminate\Support\Collection;
use RuntimeException;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Tobiasn\BitbucketApi\DataTransferObjects\PullRequestDTO;
use Tobiasn\BitbucketApi\Responses\BitbucketApiResponse;

/**
 * @see https://developer.atlassian.com/cloud/bitbucket/rest/api-group-pullrequests/#api-repositories-workspace-repo-slug-pullrequests-get
 */
class ListPullRequestsRequest extends Request
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
        return '/repositories/'.$this->workspace.'/'.$this->repoSlug.'/pullrequests';
    }

    /**
     * @return Collection<int, PullRequestDTO>
     */
    public function createDtoFromResponse(Response $response): Collection
    {
        if (! $response instanceof BitbucketApiResponse) {
            throw new RuntimeException;
        }

        return collect($response->arr('values'))->map(function ($rep) {
            return PullRequestDTO::from($rep);
        });
    }
}
