<?php

declare(strict_types=1);

use Tobiasn\BitbucketApi\DataTransferObjects\UserDTO;
use Tobiasn\BitbucketApi\Requests\Users\GetCurrentUserRequest;

it('fetches the current user', function () {
    [$connector, $mock] = mockConnector([GetCurrentUserRequest::class => 'users/current']);

    $user = $connector->send(new GetCurrentUserRequest)->dtoOrFail();

    expect($user)->toBeInstanceOf(UserDTO::class)
        ->and($user->username)->toBe('jane')
        ->and($user->has_2fa_enabled)->toBeTrue();

    $mock->assertSent(fn (GetCurrentUserRequest $request) => $request->resolveEndpoint() === '/user');
});
