<?php

namespace Tobiasn\BitbucketApi\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Tobiasn\BitbucketApi\DataTransferObjects\UserDTO;

class GetCurrentUserRequest extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/user';
    }

    public function createDtoFromResponse(Response $response): UserDTO
    {
        return new UserDTO(
            uuid: $response->json('uuid'),
            username: $response->json('username'),
            account_id: $response->json('account_id'),
            nickname: $response->json('nickname'),
            account_status: $response->json('account_status'),
            has_2fa_enabled: (bool)$response->json('has_2fa_enabled'),
        );
    }
}