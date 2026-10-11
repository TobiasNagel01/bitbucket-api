<?php

declare(strict_types=1);

use Saloon\Http\Faking\MockResponse;
use Saloon\Http\Request;
use Tobiasn\BitbucketApi\DataTransferObjects\ActivityDTO;
use Tobiasn\BitbucketApi\DataTransferObjects\CommentDTO;
use Tobiasn\BitbucketApi\DataTransferObjects\CommitDTO;
use Tobiasn\BitbucketApi\DataTransferObjects\DiffstatDTO;
use Tobiasn\BitbucketApi\DataTransferObjects\ParticipantDTO;
use Tobiasn\BitbucketApi\DataTransferObjects\PullRequestDTO;
use Tobiasn\BitbucketApi\Enums\MergeStrategy;
use Tobiasn\BitbucketApi\Enums\PullRequestState;
use Tobiasn\BitbucketApi\Requests\PullRequests\ApprovePullRequestRequest;
use Tobiasn\BitbucketApi\Requests\PullRequests\CreatePullRequestCommentRequest;
use Tobiasn\BitbucketApi\Requests\PullRequests\CreatePullRequestRequest;
use Tobiasn\BitbucketApi\Requests\PullRequests\DeclinePullRequestRequest;
use Tobiasn\BitbucketApi\Requests\PullRequests\GetPullRequestDiffRequest;
use Tobiasn\BitbucketApi\Requests\PullRequests\GetPullRequestDiffstatRequest;
use Tobiasn\BitbucketApi\Requests\PullRequests\GetPullRequestRequest;
use Tobiasn\BitbucketApi\Requests\PullRequests\ListPullRequestActivityRequest;
use Tobiasn\BitbucketApi\Requests\PullRequests\ListPullRequestCommentsRequest;
use Tobiasn\BitbucketApi\Requests\PullRequests\ListPullRequestCommitsRequest;
use Tobiasn\BitbucketApi\Requests\PullRequests\ListPullRequestsRequest;
use Tobiasn\BitbucketApi\Requests\PullRequests\MergePullRequestRequest;
use Tobiasn\BitbucketApi\Requests\PullRequests\UnapprovePullRequestRequest;
use Tobiasn\BitbucketApi\Requests\PullRequests\UpdatePullRequestRequest;

it('lists pull requests with a state filter', function () {
    [$connector, $mock] = mockConnector([ListPullRequestsRequest::class => 'pullrequests/list-page-1']);

    $prs = $connector->send(new ListPullRequestsRequest('acme', 'api', state: PullRequestState::OPEN, sort: '-updated_on'))->dtoOrFail();

    expect($prs)->toHaveCount(2)
        ->and($prs[0])->toBeInstanceOf(PullRequestDTO::class)
        ->and($prs[0]->state)->toBe(PullRequestState::OPEN)
        ->and($prs[0]->source->branch->name)->toBe('feature/x')
        ->and($prs[0]->author?->display_name)->toBe('Jane Doe');

    $mock->assertSent(fn (Request $request) => $request->query()->all() === ['state' => 'OPEN', 'sort' => '-updated_on']);
});

it('fetches a single pull request', function () {
    [$connector, $mock] = mockConnector([GetPullRequestRequest::class => 'pullrequests/merged']);

    $pr = $connector->send(new GetPullRequestRequest('acme', 'api', 2))->dtoOrFail();

    expect($pr->state)->toBe(PullRequestState::MERGED)
        ->and($pr->merge_commit?->hash)->toBe(str_repeat('b', 40))
        ->and($pr->closed_by?->display_name)->toBe('Jane Doe')
        ->and($pr->close_source_branch)->toBeTrue();

    $mock->assertSent(fn (Request $request) => $request->resolveEndpoint() === '/repositories/acme/api/pullrequests/2');
});

it('creates a pull request with the expected body', function () {
    [$connector, $mock] = mockConnector([CreatePullRequestRequest::class => 'pullrequests/get']);

    $pr = $connector->send(new CreatePullRequestRequest(
        workspace: 'acme',
        repoSlug: 'api',
        title: 'PR 1',
        sourceBranch: 'feature/x',
        destinationBranch: 'main',
        description: 'Body',
        closeSourceBranch: true,
        reviewerUuids: ['{u1}'],
    ))->dtoOrFail();

    expect($pr->id)->toBe(1);

    $mock->assertSent(fn (CreatePullRequestRequest $request) => $request->body()?->all() === [
        'title' => 'PR 1',
        'description' => 'Body',
        'source' => ['branch' => ['name' => 'feature/x']],
        'destination' => ['branch' => ['name' => 'main']],
        'close_source_branch' => true,
        'reviewers' => [['uuid' => '{u1}']],
    ]);
});

it('updates a pull request sending only the given fields', function () {
    [$connector, $mock] = mockConnector([UpdatePullRequestRequest::class => 'pullrequests/get']);

    $connector->send(new UpdatePullRequestRequest('acme', 'api', 1, title: 'New title', reviewerUuids: []))->dtoOrFail();

    $mock->assertSent(fn (UpdatePullRequestRequest $request) => $request->getMethod()->value === 'PUT'
        && $request->body()?->all() === ['title' => 'New title', 'reviewers' => []]);
});

it('merges a pull request with a strategy', function () {
    [$connector, $mock] = mockConnector([MergePullRequestRequest::class => 'pullrequests/merged']);

    $pr = $connector->send(new MergePullRequestRequest('acme', 'api', 2, message: 'Merge it', closeSourceBranch: true, mergeStrategy: MergeStrategy::SQUASH))->dtoOrFail();

    expect($pr->state)->toBe(PullRequestState::MERGED);

    $mock->assertSent(fn (MergePullRequestRequest $request) => $request->resolveEndpoint() === '/repositories/acme/api/pullrequests/2/merge'
        && $request->body()?->all() === [
            'type' => 'pullrequest',
            'message' => 'Merge it',
            'close_source_branch' => true,
            'merge_strategy' => 'squash',
        ]);
});

it('declines a pull request', function () {
    [$connector, $mock] = mockConnector([DeclinePullRequestRequest::class => 'pullrequests/get']);

    $connector->send(new DeclinePullRequestRequest('acme', 'api', 1))->dtoOrFail();

    $mock->assertSent(fn (Request $request) => $request->getMethod()->value === 'POST'
        && $request->resolveEndpoint() === '/repositories/acme/api/pullrequests/1/decline');
});

it('approves and unapproves a pull request', function () {
    [$connector, $mock] = mockConnector([
        ApprovePullRequestRequest::class => 'pullrequests/participant',
        UnapprovePullRequestRequest::class => MockResponse::make('', 204),
    ]);

    $participant = $connector->send(new ApprovePullRequestRequest('acme', 'api', 1))->dtoOrFail();

    expect($participant)->toBeInstanceOf(ParticipantDTO::class)
        ->and($participant->approved)->toBeTrue()
        ->and($participant->user->display_name)->toBe('Jane Doe');

    $response = $connector->send(new UnapprovePullRequestRequest('acme', 'api', 1));

    expect($response->status())->toBe(204)
        ->and($response->dto())->toBeNull();

    $mock->assertSent(fn (Request $request) => $request instanceof UnapprovePullRequestRequest && $request->getMethod()->value === 'DELETE');
});

it('lists and creates comments', function () {
    [$connector, $mock] = mockConnector([
        ListPullRequestCommentsRequest::class => 'pullrequests/comments',
        CreatePullRequestCommentRequest::class => 'pullrequests/comment',
    ]);

    $comments = $connector->send(new ListPullRequestCommentsRequest('acme', 'api', 1))->dtoOrFail();

    expect($comments)->toHaveCount(2)
        ->and($comments[0])->toBeInstanceOf(CommentDTO::class)
        ->and($comments[0]->inline)->toBeNull()
        ->and($comments[1]->inline?->path)->toBe('src/Foo.php')
        ->and($comments[1]->inline?->to)->toBe(12)
        ->and($comments[1]->parent?->id)->toBe(100);

    $comment = $connector->send(new CreatePullRequestCommentRequest('acme', 'api', 1, 'Looks good', inlinePath: 'src/Foo.php', inlineTo: 12, parentId: 100))->dtoOrFail();

    expect($comment->content->raw)->toBe('Looks good');

    $mock->assertSent(fn (CreatePullRequestCommentRequest $request) => $request->body()?->all() === [
        'content' => ['raw' => 'Looks good'],
        'inline' => ['path' => 'src/Foo.php', 'to' => 12],
        'parent' => ['id' => 100],
    ]);
});

it('returns the raw diff as a string', function () {
    $diff = "diff --git a/src/Foo.php b/src/Foo.php\n+++ b/src/Foo.php\n";

    [$connector, $mock] = mockConnector([
        GetPullRequestDiffRequest::class => MockResponse::make($diff, 200, ['Content-Type' => 'text/plain']),
    ]);

    $result = $connector->send(new GetPullRequestDiffRequest('acme', 'api', 1, path: 'src/Foo.php'))->dtoOrFail();

    expect($result)->toBe($diff);

    $mock->assertSent(fn (GetPullRequestDiffRequest $request) => $request->headers()->get('Accept') === 'text/plain'
        && $request->query()->all() === ['path' => 'src/Foo.php']);
});

it('fetches diffstat, activity and commits', function () {
    [$connector] = mockConnector([
        GetPullRequestDiffstatRequest::class => 'pullrequests/diffstat',
        ListPullRequestActivityRequest::class => 'pullrequests/activity',
        ListPullRequestCommitsRequest::class => 'pullrequests/commits',
    ]);

    $diffstat = $connector->send(new GetPullRequestDiffstatRequest('acme', 'api', 1))->dtoOrFail();

    expect($diffstat)->toHaveCount(2)
        ->and($diffstat[0])->toBeInstanceOf(DiffstatDTO::class)
        ->and($diffstat[0]->lines_added)->toBe(3)
        ->and($diffstat[1]->old)->toBeNull()
        ->and($diffstat[1]->new?->path)->toBe('src/Bar.php');

    $activity = $connector->send(new ListPullRequestActivityRequest('acme', 'api', 1))->dtoOrFail();

    expect($activity)->toHaveCount(2)
        ->and($activity[0])->toBeInstanceOf(ActivityDTO::class)
        ->and($activity[0]->approval)->toBeArray()
        ->and($activity[0]->update)->toBeNull()
        ->and($activity[1]->update)->toHaveKey('state', 'OPEN');

    $commits = $connector->send(new ListPullRequestCommitsRequest('acme', 'api', 1))->dtoOrFail();

    expect($commits)->toHaveCount(1)
        ->and($commits[0])->toBeInstanceOf(CommitDTO::class);
});
