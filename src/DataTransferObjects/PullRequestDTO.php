<?php

declare(strict_types=1);

namespace Tobiasn\BitbucketApi\DataTransferObjects;

use Spatie\LaravelData\Data;
use Tobiasn\BitbucketApi\Enums\PullRequestState;

final class PullRequestDTO extends Data
{
    public function __construct(
        public int $id,
        public string $title,
        public PullRequestState $state,
        public PullRequestEndpointDTO $source,
        public PullRequestEndpointDTO $destination,
        public ?AccountDTO $author = null,
        public ?string $description = null,
        public bool $close_source_branch = false,
        public ?int $comment_count = null,
        public ?int $task_count = null,
        public ?string $created_on = null,
        public ?string $updated_on = null,
        public ?CommitRefDTO $merge_commit = null,
        public ?AccountDTO $closed_by = null,
        public ?string $reason = null,
    ) {}
}
