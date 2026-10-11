<?php

declare(strict_types=1);

namespace Tobiasn\BitbucketApi\DataTransferObjects;

use Spatie\LaravelData\Data;

final class PipelineDTO extends Data
{
    public function __construct(
        public string $uuid,
        public int $build_number,
        public PipelineStateDTO $state,
        public ?string $created_on = null,
        public ?string $completed_on = null,
        public ?AccountDTO $creator = null,
        public ?PipelineTargetDTO $target = null,
        public ?int $build_seconds_used = null,
        public ?int $duration_in_seconds = null,
        /** @var array<string, mixed>|null */
        public ?array $trigger = null,
    ) {}
}
