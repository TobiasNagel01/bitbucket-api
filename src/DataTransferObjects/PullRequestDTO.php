<?php

namespace Tobiasn\BitbucketApi\DataTransferObjects;

use Spatie\LaravelData\Data;
use Tobiasn\BitbucketApi\PullRequestState;

final class PullRequestDTO extends Data
{
    public function __construct(
        public int                    $id,
        public string                 $title,
        public PullRequestState       $state,
        public PullRequestEndpointDTO $source,
        public PullRequestEndpointDTO $destination,
    )
    {
    }
}