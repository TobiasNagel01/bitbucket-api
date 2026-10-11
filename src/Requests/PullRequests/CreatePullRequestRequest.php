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
 * @see https://developer.atlassian.com/cloud/bitbucket/rest/api-group-pullrequests/#api-repositories-workspace-repo-slug-pullrequests-post
 */
final class CreatePullRequestRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    /**
     * @param  string  $workspace  This can either be the workspace ID (slug) or the workspace UUID surrounded by curly-braces, for example: {workspace UUID}.
     * @param  string  $repoSlug  This can either be the repository slug or the UUID of the repository, surrounded by curly-braces, for example: {repository UUID}.
     * @param  string  $title  Title of the pull request.
     * @param  string  $sourceBranch  Name of the source branch.
     * @param  string|null  $destinationBranch  Name of the destination branch. Defaults to the repository's main branch.
     * @param  string|null  $description  Markdown description.
     * @param  bool  $closeSourceBranch  Whether to delete the source branch after merging.
     * @param  list<string>  $reviewerUuids  UUIDs (with curly braces) of reviewers.
     */
    public function __construct(
        private readonly string $workspace,
        private readonly string $repoSlug,
        private readonly string $title,
        private readonly string $sourceBranch,
        private readonly ?string $destinationBranch = null,
        private readonly ?string $description = null,
        private readonly bool $closeSourceBranch = false,
        private readonly array $reviewerUuids = [],
    ) {}

    public function resolveEndpoint(): string
    {
        return '/repositories/'.$this->workspace.'/'.$this->repoSlug.'/pullrequests';
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaultBody(): array
    {
        return array_filter([
            'title' => $this->title,
            'description' => $this->description,
            'source' => ['branch' => ['name' => $this->sourceBranch]],
            'destination' => $this->destinationBranch !== null ? ['branch' => ['name' => $this->destinationBranch]] : null,
            'close_source_branch' => $this->closeSourceBranch,
            'reviewers' => $this->reviewerUuids !== []
                ? array_map(fn (string $uuid) => ['uuid' => $uuid], $this->reviewerUuids)
                : null,
        ], fn (mixed $value) => $value !== null);
    }

    public function createDtoFromResponse(Response $response): PullRequestDTO
    {
        return PullRequestDTO::from($response->json());
    }
}
