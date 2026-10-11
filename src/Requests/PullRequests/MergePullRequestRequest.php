<?php

declare(strict_types=1);

namespace Tobiasn\BitbucketApi\Requests\PullRequests;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasJsonBody;
use Tobiasn\BitbucketApi\DataTransferObjects\PullRequestDTO;
use Tobiasn\BitbucketApi\Enums\MergeStrategy;

/**
 * @see https://developer.atlassian.com/cloud/bitbucket/rest/api-group-pullrequests/#api-repositories-workspace-repo-slug-pullrequests-pull-request-id-merge-post
 */
final class MergePullRequestRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    /**
     * @param  string  $workspace  This can either be the workspace ID (slug) or the workspace UUID surrounded by curly-braces, for example: {workspace UUID}.
     * @param  string  $repoSlug  This can either be the repository slug or the UUID of the repository, surrounded by curly-braces, for example: {repository UUID}.
     * @param  int  $pullRequestId  The ID of the pull request.
     * @param  string|null  $message  Merge commit message.
     * @param  bool|null  $closeSourceBranch  Whether to delete the source branch after merging.
     * @param  MergeStrategy|null  $mergeStrategy  Merge strategy. Defaults to the repository setting.
     */
    public function __construct(
        private readonly string $workspace,
        private readonly string $repoSlug,
        private readonly int $pullRequestId,
        private readonly ?string $message = null,
        private readonly ?bool $closeSourceBranch = null,
        private readonly ?MergeStrategy $mergeStrategy = null,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/repositories/'.$this->workspace.'/'.$this->repoSlug.'/pullrequests/'.$this->pullRequestId.'/merge';
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaultBody(): array
    {
        return array_filter([
            'type' => 'pullrequest',
            'message' => $this->message,
            'close_source_branch' => $this->closeSourceBranch,
            'merge_strategy' => $this->mergeStrategy?->value,
        ], fn (mixed $value) => $value !== null);
    }

    public function createDtoFromResponse(Response $response): PullRequestDTO
    {
        return PullRequestDTO::from($response->json());
    }
}
