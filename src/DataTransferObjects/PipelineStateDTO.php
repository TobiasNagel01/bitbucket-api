<?php

declare(strict_types=1);

namespace Tobiasn\BitbucketApi\DataTransferObjects;

use Spatie\LaravelData\Data;
use Tobiasn\BitbucketApi\Enums\PipelineState;

final class PipelineStateDTO extends Data
{
    public function __construct(
        public PipelineState $name,
        public ?PipelineStateResultDTO $result = null,
    ) {}
}
