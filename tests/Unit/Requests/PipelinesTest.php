<?php

declare(strict_types=1);

use Saloon\Http\Faking\MockResponse;
use Saloon\Http\Request;
use Tobiasn\BitbucketApi\DataTransferObjects\PipelineDTO;
use Tobiasn\BitbucketApi\DataTransferObjects\PipelineStepDTO;
use Tobiasn\BitbucketApi\Enums\PipelineResult;
use Tobiasn\BitbucketApi\Enums\PipelineState;
use Tobiasn\BitbucketApi\Requests\Pipelines\GetPipelineRequest;
use Tobiasn\BitbucketApi\Requests\Pipelines\GetPipelineStepLogRequest;
use Tobiasn\BitbucketApi\Requests\Pipelines\ListPipelinesRequest;
use Tobiasn\BitbucketApi\Requests\Pipelines\ListPipelineStepsRequest;
use Tobiasn\BitbucketApi\Requests\Pipelines\TriggerPipelineRequest;

it('lists pipelines newest first by default', function () {
    [$connector, $mock] = mockConnector([ListPipelinesRequest::class => 'pipelines/list']);

    $pipelines = $connector->send(new ListPipelinesRequest('acme', 'api'))->dtoOrFail();

    expect($pipelines)->toHaveCount(2)
        ->and($pipelines[0])->toBeInstanceOf(PipelineDTO::class)
        ->and($pipelines[0]->state->name)->toBe(PipelineState::IN_PROGRESS)
        ->and($pipelines[0]->state->result)->toBeNull()
        ->and($pipelines[0]->completed_on)->toBeNull()
        ->and($pipelines[1]->state->result?->name)->toBe(PipelineResult::FAILED);

    $mock->assertSent(fn (Request $request) => $request->query()->all() === ['sort' => '-created_on']);
});

it('fetches a pipeline', function () {
    [$connector] = mockConnector([GetPipelineRequest::class => 'pipelines/get']);

    $pipeline = $connector->send(new GetPipelineRequest('acme', 'api', '{p42}'))->dtoOrFail();

    expect($pipeline->build_number)->toBe(42)
        ->and($pipeline->state->name)->toBe(PipelineState::COMPLETED)
        ->and($pipeline->state->result?->name)->toBe(PipelineResult::SUCCESSFUL)
        ->and($pipeline->target?->ref_name)->toBe('main')
        ->and($pipeline->creator?->display_name)->toBe('Jane Doe')
        ->and($pipeline->duration_in_seconds)->toBe(120);
});

it('triggers a pipeline with a custom selector, commit and variables', function () {
    [$connector, $mock] = mockConnector([TriggerPipelineRequest::class => 'pipelines/get']);

    $connector->send(new TriggerPipelineRequest(
        workspace: 'acme',
        repoSlug: 'api',
        refName: 'main',
        commitHash: 'abc123',
        customPipeline: 'deploy',
        variables: [['key' => 'ENV', 'value' => 'prod', 'secured' => false]],
    ))->dtoOrFail();

    $mock->assertSent(fn (TriggerPipelineRequest $request) => $request->body()?->all() === [
        'target' => [
            'type' => 'pipeline_ref_target',
            'ref_type' => 'branch',
            'ref_name' => 'main',
            'commit' => ['type' => 'commit', 'hash' => 'abc123'],
            'selector' => ['type' => 'custom', 'pattern' => 'deploy'],
        ],
        'variables' => [['key' => 'ENV', 'value' => 'prod', 'secured' => false]],
    ]);
});

it('lists steps and fetches a step log', function () {
    [$connector, $mock] = mockConnector([
        ListPipelineStepsRequest::class => 'pipelines/steps',
        GetPipelineStepLogRequest::class => MockResponse::make("+ composer install\nDone.\n", 200, ['Content-Type' => 'application/octet-stream']),
    ]);

    $steps = $connector->send(new ListPipelineStepsRequest('acme', 'api', '{p42}'))->dtoOrFail();

    expect($steps)->toHaveCount(1)
        ->and($steps[0])->toBeInstanceOf(PipelineStepDTO::class)
        ->and($steps[0]->name)->toBe('Build')
        ->and($steps[0]->state->result?->name)->toBe(PipelineResult::SUCCESSFUL)
        ->and($steps[0]->script_commands)->toHaveCount(1);

    $log = $connector->send(new GetPipelineStepLogRequest('acme', 'api', '{p42}', '{s1}'))->dtoOrFail();

    expect($log)->toBe("+ composer install\nDone.\n");

    $mock->assertSent(fn (GetPipelineStepLogRequest $request) => $request->resolveEndpoint() === '/repositories/acme/api/pipelines/{p42}/steps/{s1}/log'
        && $request->headers()->get('Accept') === 'application/octet-stream');
});
