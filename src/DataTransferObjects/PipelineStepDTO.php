<?php

declare(strict_types=1);

namespace Tobiasn\BitbucketApi\DataTransferObjects;

use Spatie\LaravelData\Data;

final class PipelineStepDTO extends Data
{
    public function __construct(
        public string $uuid,
        public PipelineStateDTO $state,
        public ?string $name = null,
        public ?string $started_on = null,
        public ?string $completed_on = null,
        public ?int $duration_in_seconds = null,
        /** @var array<string, mixed>|null */
        public ?array $image = null,
        /** @var list<array<string, mixed>>|null */
        public ?array $script_commands = null,
    ) {}
}
