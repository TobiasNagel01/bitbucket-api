<?php

declare(strict_types=1);

namespace Tobiasn\BitbucketApi\Requests\Pipelines;

use Illuminate\Support\Collection;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\PaginationPlugin\Contracts\Paginatable;
use Tobiasn\BitbucketApi\DataTransferObjects\PipelineStepDTO;
use Tobiasn\BitbucketApi\Requests\Concerns\CreatesCollectionDto;

/**
 * @see https://developer.atlassian.com/cloud/bitbucket/rest/api-group-pipelines/#api-repositories-workspace-repo-slug-pipelines-pipeline-uuid-steps-get
 */
final class ListPipelineStepsRequest extends Request implements Paginatable
{
    use CreatesCollectionDto;

    protected Method $method = Method::GET;

    /**
     * @param  string  $workspace  This can either be the workspace ID (slug) or the workspace UUID surrounded by curly-braces, for example: {workspace UUID}.
     * @param  string  $repoSlug  This can either be the repository slug or the UUID of the repository, surrounded by curly-braces, for example: {repository UUID}.
     * @param  string  $pipelineUuid  UUID of the pipeline surrounded by curly braces.
     * @param  int|null  $pagelen  Page size.
     */
    public function __construct(
        private readonly string $workspace,
        private readonly string $repoSlug,
        private readonly string $pipelineUuid,
        private readonly ?int $pagelen = null,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/repositories/'.$this->workspace.'/'.$this->repoSlug.'/pipelines/'.$this->pipelineUuid.'/steps';
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaultQuery(): array
    {
        return array_filter(['pagelen' => $this->pagelen], fn (mixed $value) => $value !== null);
    }

    /**
     * @return Collection<int, PipelineStepDTO>
     */
    public function createDtoFromResponse(Response $response): Collection
    {
        return $this->mapValues($response, PipelineStepDTO::class);
    }
}
