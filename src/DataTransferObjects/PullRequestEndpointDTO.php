<?php

declare(strict_types=1);

namespace Tobiasn\BitbucketApi\DataTransferObjects;

use Spatie\LaravelData\Data;

final class PullRequestEndpointDTO extends Data
{
    public function __construct(
        public RepositoryDTO $repository,
        public BranchDTO $branch,
    ) {}
}
