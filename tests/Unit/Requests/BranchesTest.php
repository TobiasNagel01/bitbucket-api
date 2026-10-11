<?php

declare(strict_types=1);

use Saloon\Http\Request;
use Tobiasn\BitbucketApi\DataTransferObjects\BranchDTO;
use Tobiasn\BitbucketApi\DataTransferObjects\CommitDTO;
use Tobiasn\BitbucketApi\Requests\Repositories\GetBranchRequest;
use Tobiasn\BitbucketApi\Requests\Repositories\ListBranchesRequest;

it('lists branches', function () {
    [$connector] = mockConnector([ListBranchesRequest::class => 'branches/list']);

    $branches = $connector->send(new ListBranchesRequest('acme', 'api'))->dtoOrFail();

    expect($branches)->toHaveCount(2)
        ->and($branches[0])->toBeInstanceOf(BranchDTO::class)
        ->and($branches[0]->name)->toBe('main')
        ->and($branches[0]->target)->toBeInstanceOf(CommitDTO::class);
});

it('fetches a branch and url-encodes its name', function () {
    [$connector, $mock] = mockConnector([GetBranchRequest::class => 'branches/get']);

    $branch = $connector->send(new GetBranchRequest('acme', 'api', 'feature/login'))->dtoOrFail();

    expect($branch->name)->toBe('main');

    $mock->assertSent(fn (Request $request) => $request->resolveEndpoint() === '/repositories/acme/api/refs/branches/feature%2Flogin');
});
