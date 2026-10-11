<?php

declare(strict_types=1);

namespace Tobiasn\BitbucketApi\Requests\Users;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Tobiasn\BitbucketApi\DataTransferObjects\UserDTO;

/**
 * @see https://developer.atlassian.com/cloud/bitbucket/rest/api-group-users/#api-user-get
 */
final class GetCurrentUserRequest extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/user';
    }

    public function createDtoFromResponse(Response $response): UserDTO
    {
        return UserDTO::from($response->json());
    }
}
