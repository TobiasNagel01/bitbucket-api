<?php

declare(strict_types=1);

use Saloon\Http\Request;
use Tobiasn\BitbucketApi\DataTransferObjects\CommitDTO;
use Tobiasn\BitbucketApi\Requests\Repositories\GetCommitRequest;
use Tobiasn\BitbucketApi\Requests\Repositories\ListCommitsRequest;

it('fetches a commit', function () {
    [$connector] = mockConnector([GetCommitRequest::class => 'commits/get']);

    $commit = $connector->send(new GetCommitRequest('acme', 'api', 'eeee'))->dtoOrFail();

    expect($commit)->toBeInstanceOf(CommitDTO::class)
        ->and($commit->message)->toBe('fix: thing')
        ->and($commit->author->raw)->toBe('Jane Doe <jane@example.com>')
        ->and($commit->author->user?->display_name)->toBe('Jane Doe')
        ->and($commit->parents)->toHaveCount(1);
});

it('lists commits from a revision with filters', function () {
    [$connector, $mock] = mockConnector([ListCommitsRequest::class => 'commits/list-page-1']);

    $commits = $connector->send(new ListCommitsRequest('acme', 'api', revision: 'develop', exclude: 'main', path: 'src'))->dtoOrFail();

    expect($commits)->toHaveCount(2);

    $mock->assertSent(fn (Request $request) => $request->resolveEndpoint() === '/repositories/acme/api/commits/develop'
        && $request->query()->all() === ['exclude' => 'main', 'path' => 'src']);
});
