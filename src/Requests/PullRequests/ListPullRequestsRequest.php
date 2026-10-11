<?php

declare(strict_types=1);

namespace Tobiasn\BitbucketApi\Requests\PullRequests;

use Illuminate\Support\Collection;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\PaginationPlugin\Contracts\Paginatable;
use Tobiasn\BitbucketApi\DataTransferObjects\PullRequestDTO;
use Tobiasn\BitbucketApi\Enums\PullRequestState;
use Tobiasn\BitbucketApi\Requests\Concerns\CreatesCollectionDto;

/**
 * @see https://developer.atlassian.com/cloud/bitbucket/rest/api-group-pullrequests/#api-repositories-workspace-repo-slug-pullrequests-get
 */
final class ListPullRequestsRequest extends Request implements Paginatable
{
    use CreatesCollectionDto;

    protected Method $method = Method::GET;

    /**
     * @param  string  $workspace  This can either be the workspace ID (slug) or the workspace UUID surrounded by curly-braces, for example: {workspace UUID}.
     * @param  string  $repoSlug  This can either be the repository slug or the UUID of the repository, surrounded by curly-braces, for example: {repository UUID}.
     * @param  PullRequestState|null  $state  Only return pull requests in this state. Defaults to OPEN on the API side. Use `$q` to combine several states.
     * @param  string|null  $q  Bitbucket query string, for example: `state="OPEN" OR state="MERGED"`.
     * @param  string|null  $sort  Field to sort by, prefix with `-` for descending, for example: `-updated_on`.
     * @param  int|null  $pagelen  Page size.
     */
    public function __construct(
        private readonly string $workspace,
        private readonly string $repoSlug,
        private readonly ?PullRequestState $state = null,
        private readonly ?string $q = null,
        private readonly ?string $sort = null,
        private readonly ?int $pagelen = null,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/repositories/'.$this->workspace.'/'.$this->repoSlug.'/pullrequests';
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaultQuery(): array
    {
        return array_filter([
            'state' => $this->state?->value,
            'q' => $this->q,
            'sort' => $this->sort,
            'pagelen' => $this->pagelen,
        ], fn (mixed $value) => $value !== null);
    }

    /**
     * @return Collection<int, PullRequestDTO>
     */
    public function createDtoFromResponse(Response $response): Collection
    {
        return $this->mapValues($response, PullRequestDTO::class);
    }
}
