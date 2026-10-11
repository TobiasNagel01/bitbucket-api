<?php

declare(strict_types=1);

namespace Tobiasn\BitbucketApi\Requests\Repositories;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasJsonBody;
use Tobiasn\BitbucketApi\DataTransferObjects\RepositoryDTO;
use Tobiasn\BitbucketApi\Enums\ForkPolicy;

/**
 * @see https://developer.atlassian.com/cloud/bitbucket/rest/api-group-repositories/#api-repositories-workspace-repo-slug-post
 */
final class CreateRepositoryRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    /**
     * @param  string  $workspace  This can either be the workspace ID (slug) or the workspace UUID surrounded by curly-braces, for example: {workspace UUID}.
     * @param  string  $repoSlug  Slug of the repository to create.
     * @param  string|null  $projectKey  Key of the project the repository belongs to. Required for workspaces with more than one project.
     * @param  bool  $isPrivate  Whether the repository is private.
     * @param  string|null  $name  Display name. Defaults to the slug.
     * @param  string|null  $description  Repository description.
     * @param  ForkPolicy|null  $forkPolicy  Fork policy.
     * @param  string|null  $language  Primary programming language.
     */
    public function __construct(
        private readonly string $workspace,
        private readonly string $repoSlug,
        private readonly ?string $projectKey = null,
        private readonly bool $isPrivate = true,
        private readonly ?string $name = null,
        private readonly ?string $description = null,
        private readonly ?ForkPolicy $forkPolicy = null,
        private readonly ?string $language = null,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/repositories/'.$this->workspace.'/'.$this->repoSlug;
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaultBody(): array
    {
        return array_filter([
            'scm' => 'git',
            'is_private' => $this->isPrivate,
            'project' => $this->projectKey !== null ? ['key' => $this->projectKey] : null,
            'name' => $this->name,
            'description' => $this->description,
            'fork_policy' => $this->forkPolicy?->value,
            'language' => $this->language,
        ], fn (mixed $value) => $value !== null);
    }

    public function createDtoFromResponse(Response $response): RepositoryDTO
    {
        return RepositoryDTO::from($response->json());
    }
}
