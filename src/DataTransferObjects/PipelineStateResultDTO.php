<?php

declare(strict_types=1);

namespace Tobiasn\BitbucketApi\DataTransferObjects;

use Spatie\LaravelData\Data;
use Tobiasn\BitbucketApi\Enums\PipelineResult;

final class PipelineStateResultDTO extends Data
{
    public function __construct(
        public PipelineResult $name,
    ) {}
}
