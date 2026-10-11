<?php

declare(strict_types=1);

namespace Tobiasn\BitbucketApi\Requests\Pipelines;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Tobiasn\BitbucketApi\DataTransferObjects\PipelineDTO;

/**
 * @see https://developer.atlassian.com/cloud/bitbucket/rest/api-group-pipelines/#api-repositories-workspace-repo-slug-pipelines-pipeline-uuid-get
 */
final class GetPipelineRequest extends Request
{
    protected Method $method = Method::GET;

    /**
     * @param  string  $workspace  This can either be the workspace ID (slug) or the workspace UUID surrounded by curly-braces, for example: {workspace UUID}.
     * @param  string  $repoSlug  This can either be the repository slug or the UUID of the repository, surrounded by curly-braces, for example: {repository UUID}.
     * @param  string  $pipelineUuid  UUID of the pipeline surrounded by curly braces, or its build number.
     */
    public function __construct(
        private readonly string $workspace,
        private readonly string $repoSlug,
        private readonly string $pipelineUuid,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/repositories/'.$this->workspace.'/'.$this->repoSlug.'/pipelines/'.$this->pipelineUuid;
    }

    public function createDtoFromResponse(Response $response): PipelineDTO
    {
        return PipelineDTO::from($response->json());
    }
}
