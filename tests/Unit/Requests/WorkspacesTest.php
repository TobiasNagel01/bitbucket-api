<?php

declare(strict_types=1);

use Tobiasn\BitbucketApi\DataTransferObjects\WorkspaceAccessDTO;
use Tobiasn\BitbucketApi\DataTransferObjects\WorkspaceBaseDTO;
use Tobiasn\BitbucketApi\Requests\Workspaces\GetWorkspaceRequest;
use Tobiasn\BitbucketApi\Requests\Workspaces\ListWorkspacesRequest;

it('lists workspaces', function () {
    [$connector, $mock] = mockConnector([ListWorkspacesRequest::class => 'workspaces/list']);

    $workspaces = $connector->send(new ListWorkspacesRequest(administrator: true))->dtoOrFail();

    expect($workspaces)->toHaveCount(1)
        ->and($workspaces[0])->toBeInstanceOf(WorkspaceAccessDTO::class)
        ->and($workspaces[0]->administrator)->toBeTrue()
        ->and($workspaces[0]->workspace)->toBeInstanceOf(WorkspaceBaseDTO::class)
        ->and($workspaces[0]->workspace->slug)->toBe('acme')
        ->and($workspaces[0]->workspace->name)->toBe('Acme Inc');

    $mock->assertSent(fn (ListWorkspacesRequest $request) => $request->resolveEndpoint() === '/user/workspaces'
        && $request->query()->all() === ['administrator' => 'true']);
});

it('lists workspaces without filters', function () {
    [$connector, $mock] = mockConnector([ListWorkspacesRequest::class => 'workspaces/list']);

    $connector->send(new ListWorkspacesRequest)->dtoOrFail();

    $mock->assertSent(fn (ListWorkspacesRequest $request) => $request->query()->all() === []);
});

it('fetches a workspace', function () {
    [$connector] = mockConnector([GetWorkspaceRequest::class => 'workspaces/get']);

    $workspace = $connector->send(new GetWorkspaceRequest('acme'))->dtoOrFail();

    expect($workspace->name)->toBe('Acme Inc')
        ->and($workspace->is_private)->toBeTrue();
});
