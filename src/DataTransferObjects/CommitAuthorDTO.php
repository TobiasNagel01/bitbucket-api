<?php

declare(strict_types=1);

namespace Tobiasn\BitbucketApi\DataTransferObjects;

use Spatie\LaravelData\Data;

final class CommitAuthorDTO extends Data
{
    public function __construct(
        public string $raw,
        public ?AccountDTO $user = null,
    ) {}
}
