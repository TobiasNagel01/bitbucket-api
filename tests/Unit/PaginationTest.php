<?php

declare(strict_types=1);

use Saloon\Http\Request;
use Tobiasn\BitbucketApi\DataTransferObjects\CommitDTO;
use Tobiasn\BitbucketApi\DataTransferObjects\PullRequestDTO;
use Tobiasn\BitbucketApi\Enums\PullRequestState;
use Tobiasn\BitbucketApi\Pagination\BitbucketPaginator;
use Tobiasn\BitbucketApi\Requests\PullRequests\ListPullRequestsRequest;
use Tobiasn\BitbucketApi\Requests\Repositories\ListCommitsRequest;

it('follows page-numbered next links and collects every DTO', function () {
    [$connector, $mock] = mockConnector([
        apiFixture('pullrequests/list-page-1'),
        apiFixture('pullrequests/list-page-2'),
    ]);

    $paginator = $connector->paginate(new ListPullRequestsRequest('acme', 'api', state: PullRequestState::OPEN));

    expect($paginator)->toBeInstanceOf(BitbucketPaginator::class);

    $items = $paginator->collect()->all();

    expect($items)->toHaveCount(3)
        ->and($items[0])->toBeInstanceOf(PullRequestDTO::class)
        ->and(array_map(fn (PullRequestDTO $pr) => $pr->id, $items))->toBe([1, 2, 3]);

    $mock->assertSentCount(2);
    $mock->assertSentInOrder([
        fn (Request $request) => $request->query()->all() === ['state' => 'OPEN'],
        fn (Request $request) => $request->query()->all() === ['state' => 'OPEN', 'pagelen' => '2', 'page' => '2'],
    ]);
});

it('follows cursor-based next links for commits', function () {
    [$connector, $mock] = mockConnector([
        apiFixture('commits/list-page-1'),
        apiFixture('commits/list-page-2'),
    ]);

    $commits = $connector->paginate(new ListCommitsRequest('acme', 'api'))->collect()->all();

    expect($commits)->toHaveCount(3)
        ->and($commits[2])->toBeInstanceOf(CommitDTO::class)
        ->and($commits[2]->message)->toBe('three');

    $mock->assertSentInOrder([
        fn (Request $request) => $request->query()->all() === [],
        fn (Request $request) => $request->query()->get('ctx') === 'opaque-cursor-123',
    ]);
});

it('applies a per-page limit to the first request and respects max pages', function () {
    [$connector, $mock] = mockConnector([
        apiFixture('pullrequests/list-page-1'),
        apiFixture('pullrequests/list-page-2'),
    ]);

    $items = $connector->paginate(new ListPullRequestsRequest('acme', 'api'))
        ->setPerPageLimit(2)
        ->setMaxPages(1)
        ->collect()
        ->all();

    expect($items)->toHaveCount(2);

    $mock->assertSentCount(1);
    $mock->assertSent(fn (Request $request) => $request->query()->get('pagelen') === 2);
});

it('still returns only the first page when sending without the paginator', function () {
    [$connector] = mockConnector([ListPullRequestsRequest::class => 'pullrequests/list-page-1']);

    $items = $connector->send(new ListPullRequestsRequest('acme', 'api'))->dtoOrFail();

    expect($items)->toHaveCount(2);
});
