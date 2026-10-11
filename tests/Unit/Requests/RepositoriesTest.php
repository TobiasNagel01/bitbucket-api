<?php

declare(strict_types=1);

use Illuminate\Support\Collection;
use Saloon\Http\Request;
use Tobiasn\BitbucketApi\DataTransferObjects\RepositoryDTO;
use Tobiasn\BitbucketApi\DataTransferObjects\WorkspaceDTO;
use Tobiasn\BitbucketApi\Enums\ForkPolicy;
use Tobiasn\BitbucketApi\Requests\Repositories\CreateRepositoryRequest;
use Tobiasn\BitbucketApi\Requests\Repositories\GetRepositoryRequest;
use Tobiasn\BitbucketApi\Requests\Repositories\ListRepositoriesInWorkspaceRequest;

it('fetches a single repository', function () {
    [$connector, $mock] = mockConnector([GetRepositoryRequest::class => 'repositories/get']);

    $repo = $connector->send(new GetRepositoryRequest('acme', 'api'))->dtoOrFail();

    expect($repo)->toBeInstanceOf(RepositoryDTO::class)
        ->and($repo->full_name)->toBe('acme/api')
        ->and($repo->is_private)->toBeTrue()
        ->and($repo->owner->display_name)->toBe('Jane Doe')
        ->and($repo->workspace)->toBeInstanceOf(WorkspaceDTO::class)
        ->and($repo->workspace->slug)->toBe('acme');

    $mock->assertSent(fn (Request $request) => $request->resolveEndpoint() === '/repositories/acme/api');
});

it('lists repositories in a workspace with filters', function () {
    [$connector, $mock] = mockConnector([ListRepositoriesInWorkspaceRequest::class => 'repositories/list']);

    $repos = $connector->send(new ListRepositoriesInWorkspaceRequest('acme', role: 'admin', q: 'name ~ "a"', sort: '-updated_on', pagelen: 10))->dtoOrFail();

    expect($repos)->toBeInstanceOf(Collection::class)->toHaveCount(2)
        ->and($repos[0])->toBeInstanceOf(RepositoryDTO::class)
        ->and($repos[1]->description)->toBeNull()
        ->and($repos[1]->language)->toBeNull();

    $mock->assertSent(fn (Request $request) => $request->query()->all() === [
        'role' => 'admin',
        'q' => 'name ~ "a"',
        'sort' => '-updated_on',
        'pagelen' => 10,
    ]);
});

it('creates a repository with a JSON body', function () {
    [$connector, $mock] = mockConnector([CreateRepositoryRequest::class => 'repositories/get']);

    $repo = $connector->send(new CreateRepositoryRequest(
        workspace: 'acme',
        repoSlug: 'api',
        projectKey: 'PROJ',
        isPrivate: true,
        description: 'The API',
        forkPolicy: ForkPolicy::NO_PUBLIC_FORKS,
    ))->dtoOrFail();

    expect($repo->slug)->toBe('api');

    $mock->assertSent(fn (CreateRepositoryRequest $request) => $request->body()?->all() === [
        'scm' => 'git',
        'is_private' => true,
        'project' => ['key' => 'PROJ'],
        'description' => 'The API',
        'fork_policy' => 'no_public_forks',
    ]);
});
