<?php

declare(strict_types=1);

namespace Tobiasn\BitbucketApi\Requests\PullRequests;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasJsonBody;
use Tobiasn\BitbucketApi\DataTransferObjects\PullRequestDTO;

/**
 * @see https://developer.atlassian.com/cloud/bitbucket/rest/api-group-pullrequests/#api-repositories-workspace-repo-slug-pullrequests-pull-request-id-put
 */
final class UpdatePullRequestRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::PUT;

    /**
     * @param  string  $workspace  This can either be the workspace ID (slug) or the workspace UUID surrounded by curly-braces, for example: {workspace UUID}.
     * @param  string  $repoSlug  This can either be the repository slug or the UUID of the repository, surrounded by curly-braces, for example: {repository UUID}.
     * @param  int  $pullRequestId  The ID of the pull request.
     * @param  string|null  $title  New title.
     * @param  string|null  $description  New Markdown description.
     * @param  string|null  $destinationBranch  New destination branch name.
     * @param  bool|null  $closeSourceBranch  Whether to delete the source branch after merging.
     * @param  list<string>|null  $reviewerUuids  Replacement reviewer list (UUIDs with curly braces). Null leaves reviewers untouched.
     */
    public function __construct(
        private readonly string $workspace,
        private readonly string $repoSlug,
        private readonly int $pullRequestId,
        private readonly ?string $title = null,
        private readonly ?string $description = null,
        private readonly ?string $destinationBranch = null,
        private readonly ?bool $closeSourceBranch = null,
        private readonly ?array $reviewerUuids = null,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/repositories/'.$this->workspace.'/'.$this->repoSlug.'/pullrequests/'.$this->pullRequestId;
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaultBody(): array
    {
        return array_filter([
            'title' => $this->title,
            'description' => $this->description,
            'destination' => $this->destinationBranch !== null ? ['branch' => ['name' => $this->destinationBranch]] : null,
            'close_source_branch' => $this->closeSourceBranch,
            'reviewers' => $this->reviewerUuids !== null
                ? array_map(fn (string $uuid) => ['uuid' => $uuid], $this->reviewerUuids)
                : null,
        ], fn (mixed $value) => $value !== null);
    }

    public function createDtoFromResponse(Response $response): PullRequestDTO
    {
        return PullRequestDTO::from($response->json());
    }
}
