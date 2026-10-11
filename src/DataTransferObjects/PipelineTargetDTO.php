<?php

declare(strict_types=1);

namespace Tobiasn\BitbucketApi\DataTransferObjects;

use Spatie\LaravelData\Data;

final class PipelineTargetDTO extends Data
{
    public function __construct(
        public ?string $ref_type = null,
        public ?string $ref_name = null,
        public ?string $type = null,
        /** @var array<string, mixed>|null */
        public ?array $commit = null,
        /** @var array<string, mixed>|null */
        public ?array $selector = null,
    ) {}
}
