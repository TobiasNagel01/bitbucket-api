<?php

declare(strict_types=1);

namespace Tobiasn\BitbucketApi\Requests\PullRequests;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Tobiasn\BitbucketApi\DataTransferObjects\PullRequestDTO;

/**
 * @see https://developer.atlassian.com/cloud/bitbucket/rest/api-group-pullrequests/#api-repositories-workspace-repo-slug-pullrequests-pull-request-id-decline-post
 */
final class DeclinePullRequestRequest extends Request
{
    protected Method $method = Method::POST;

    /**
     * @param  string  $workspace  This can either be the workspace ID (slug) or the workspace UUID surrounded by curly-braces, for example: {workspace UUID}.
     * @param  string  $repoSlug  This can either be the repository slug or the UUID of the repository, surrounded by curly-braces, for example: {repository UUID}.
     * @param  int  $pullRequestId  The ID of the pull request.
     */
    public function __construct(
        private readonly string $workspace,
        private readonly string $repoSlug,
        private readonly int $pullRequestId,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/repositories/'.$this->workspace.'/'.$this->repoSlug.'/pullrequests/'.$this->pullRequestId.'/decline';
    }

    public function createDtoFromResponse(Response $response): PullRequestDTO
    {
        return PullRequestDTO::from($response->json());
    }
}
