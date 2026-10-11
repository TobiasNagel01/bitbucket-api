<?php

declare(strict_types=1);

namespace Tobiasn\BitbucketApi\Requests\Repositories;

use Illuminate\Support\Collection;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\PaginationPlugin\Contracts\Paginatable;
use Tobiasn\BitbucketApi\DataTransferObjects\CommitDTO;
use Tobiasn\BitbucketApi\Requests\Concerns\CreatesCollectionDto;

/**
 * Lists commits, newest first. This endpoint paginates with an opaque `ctx` cursor
 * rather than page numbers, which the paginator handles transparently.
 *
 * @see https://developer.atlassian.com/cloud/bitbucket/rest/api-group-commits/#api-repositories-workspace-repo-slug-commits-get
 * @see https://developer.atlassian.com/cloud/bitbucket/rest/api-group-commits/#api-repositories-workspace-repo-slug-commits-revision-get
 */
final class ListCommitsRequest extends Request implements Paginatable
{
    use CreatesCollectionDto;

    protected Method $method = Method::GET;

    /**
     * @param  string  $workspace  This can either be the workspace ID (slug) or the workspace UUID surrounded by curly-braces, for example: {workspace UUID}.
     * @param  string  $repoSlug  This can either be the repository slug or the UUID of the repository, surrounded by curly-braces, for example: {repository UUID}.
     * @param  string|null  $revision  Branch name, tag or commit hash to start from. Defaults to the main branch.
     * @param  string|null  $exclude  Branch name, tag or commit hash whose ancestors are excluded.
     * @param  string|null  $path  Only return commits that touch this path.
     * @param  int|null  $pagelen  Page size.
     */
    public function __construct(
        private readonly string $workspace,
        private readonly string $repoSlug,
        private readonly ?string $revision = null,
        private readonly ?string $exclude = null,
        private readonly ?string $path = null,
        private readonly ?int $pagelen = null,
    ) {}

    public function resolveEndpoint(): string
    {
        $endpoint = '/repositories/'.$this->workspace.'/'.$this->repoSlug.'/commits';

        return $this->revision !== null ? $endpoint.'/'.rawurlencode($this->revision) : $endpoint;
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaultQuery(): array
    {
        return array_filter([
            'exclude' => $this->exclude,
            'path' => $this->path,
            'pagelen' => $this->pagelen,
        ], fn (mixed $value) => $value !== null);
    }

    /**
     * @return Collection<int, CommitDTO>
     */
    public function createDtoFromResponse(Response $response): Collection
    {
        return $this->mapValues($response, CommitDTO::class);
    }
}
