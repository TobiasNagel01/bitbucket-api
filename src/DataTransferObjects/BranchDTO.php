<?php

declare(strict_types=1);

namespace Tobiasn\BitbucketApi\DataTransferObjects;

use Spatie\LaravelData\Data;

final class BranchDTO extends Data
{
    public function __construct(
        public string $name,
        public ?CommitDTO $target = null,
    ) {}
}
