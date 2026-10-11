<?php

declare(strict_types=1);

namespace Tobiasn\BitbucketApi\Requests\Workspaces;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Tobiasn\BitbucketApi\DataTransferObjects\WorkspaceDTO;

/**
 * @see https://developer.atlassian.com/cloud/bitbucket/rest/api-group-workspaces/#api-workspaces-workspace-get
 */
final class GetWorkspaceRequest extends Request
{
    protected Method $method = Method::GET;

    /**
     * @param  string  $workspace  This can either be the workspace ID (slug) or the workspace UUID surrounded by curly-braces, for example: {workspace UUID}.
     */
    public function __construct(
        private readonly string $workspace,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/workspaces/'.$this->workspace;
    }

    public function createDtoFromResponse(Response $response): WorkspaceDTO
    {
        return WorkspaceDTO::from($response->json());
    }
}
