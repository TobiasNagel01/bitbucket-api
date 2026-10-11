<?php

declare(strict_types=1);

namespace Tobiasn\BitbucketApi\Requests\Pipelines;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

/**
 * Returns the raw log output of a pipeline step as a string.
 *
 * @see https://developer.atlassian.com/cloud/bitbucket/rest/api-group-pipelines/#api-repositories-workspace-repo-slug-pipelines-pipeline-uuid-steps-step-uuid-log-get
 */
final class GetPipelineStepLogRequest extends Request
{
    protected Method $method = Method::GET;

    /**
     * @param  string  $workspace  This can either be the workspace ID (slug) or the workspace UUID surrounded by curly-braces, for example: {workspace UUID}.
     * @param  string  $repoSlug  This can either be the repository slug or the UUID of the repository, surrounded by curly-braces, for example: {repository UUID}.
     * @param  string  $pipelineUuid  UUID of the pipeline surrounded by curly braces.
     * @param  string  $stepUuid  UUID of the step surrounded by curly braces.
     */
    public function __construct(
        private readonly string $workspace,
        private readonly string $repoSlug,
        private readonly string $pipelineUuid,
        private readonly string $stepUuid,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/repositories/'.$this->workspace.'/'.$this->repoSlug.'/pipelines/'.$this->pipelineUuid.'/steps/'.$this->stepUuid.'/log';
    }

    /**
     * @return array<string, string>
     */
    protected function defaultHeaders(): array
    {
        return ['Accept' => 'application/octet-stream'];
    }

    public function createDtoFromResponse(Response $response): string
    {
        return $response->body();
    }
}
