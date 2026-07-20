<?php

namespace Tobiasn\BitbucketApi\DataTransferObjects;

use Spatie\LaravelData\Data;

final class BranchDTO extends Data
{
    public function __construct(
        public string $name,
    )
    {
    }
}