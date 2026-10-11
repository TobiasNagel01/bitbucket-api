<?php

declare(strict_types=1);

namespace Tobiasn\BitbucketApi\DataTransferObjects;

use Spatie\LaravelData\Data;

final class CommitRefDTO extends Data
{
    public function __construct(
        public string $hash,
    ) {}
}
