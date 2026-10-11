<?php

declare(strict_types=1);

use Tobiasn\BitbucketApi\DataTransferObjects\WorkspaceDTO;
use Tobiasn\BitbucketApi\Requests\Workspaces\GetWorkspaceRequest;
use Tobiasn\BitbucketApi\Requests\Workspaces\ListWorkspacesRequest;

it('lists workspaces', function () {
    [$connector, $mock] = mockConnector([ListWorkspacesRequest::class => 'workspaces/list']);

    $workspaces = $connector->send(new ListWorkspacesRequest(role: 'member'))->dtoOrFail();

    expect($workspaces)->toHaveCount(1)
        ->and($workspaces[0])->toBeInstanceOf(WorkspaceDTO::class)
        ->and($workspaces[0]->slug)->toBe('acme');

    $mock->assertSent(fn (ListWorkspacesRequest $request) => $request->query()->all() === ['role' => 'member']);
});

it('fetches a workspace', function () {
    [$connector] = mockConnector([GetWorkspaceRequest::class => 'workspaces/get']);

    $workspace = $connector->send(new GetWorkspaceRequest('acme'))->dtoOrFail();

    expect($workspace->name)->toBe('Acme Inc')
        ->and($workspace->is_private)->toBeTrue();
});
