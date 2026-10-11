<?php

declare(strict_types=1);

namespace Tobiasn\BitbucketApi\Requests\Pipelines;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasJsonBody;
use Tobiasn\BitbucketApi\DataTransferObjects\PipelineDTO;

/**
 * Runs a pipeline on a branch or tag, optionally pinned to a commit and/or using a custom pipeline definition.
 *
 * @see https://developer.atlassian.com/cloud/bitbucket/rest/api-group-pipelines/#api-repositories-workspace-repo-slug-pipelines-post
 */
final class TriggerPipelineRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    /**
     * @param  string  $workspace  This can either be the workspace ID (slug) or the workspace UUID surrounded by curly-braces, for example: {workspace UUID}.
     * @param  string  $repoSlug  This can either be the repository slug or the UUID of the repository, surrounded by curly-braces, for example: {repository UUID}.
     * @param  string  $refName  Branch or tag name to run the pipeline on.
     * @param  string  $refType  `branch` or `tag`.
     * @param  string|null  $commitHash  Run against this specific commit on the ref.
     * @param  string|null  $customPipeline  Name of a custom pipeline defined under `pipelines.custom` in bitbucket-pipelines.yml.
     * @param  list<array{key: string, value: string, secured?: bool}>  $variables  Pipeline variables.
     */
    public function __construct(
        private readonly string $workspace,
        private readonly string $repoSlug,
        private readonly string $refName,
        private readonly string $refType = 'branch',
        private readonly ?string $commitHash = null,
        private readonly ?string $customPipeline = null,
        private readonly array $variables = [],
    ) {}

    public function resolveEndpoint(): string
    {
        return '/repositories/'.$this->workspace.'/'.$this->repoSlug.'/pipelines';
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaultBody(): array
    {
        $target = [
            'type' => 'pipeline_ref_target',
            'ref_type' => $this->refType,
            'ref_name' => $this->refName,
        ];

        if ($this->commitHash !== null) {
            $target['commit'] = ['type' => 'commit', 'hash' => $this->commitHash];
        }

        if ($this->customPipeline !== null) {
            $target['selector'] = ['type' => 'custom', 'pattern' => $this->customPipeline];
        }

        return array_filter([
            'target' => $target,
            'variables' => $this->variables !== [] ? $this->variables : null,
        ], fn (mixed $value) => $value !== null);
    }

    public function createDtoFromResponse(Response $response): PipelineDTO
    {
        return PipelineDTO::from($response->json());
    }
}
