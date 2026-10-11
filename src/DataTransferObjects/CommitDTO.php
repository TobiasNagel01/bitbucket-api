<?php

declare(strict_types=1);

namespace Tobiasn\BitbucketApi\DataTransferObjects;

use Spatie\LaravelData\Data;

final class CommitDTO extends Data
{
    public function __construct(
        public string $hash,
        public string $date,
        public CommitAuthorDTO $author,
        public ?string $message = null,
        /** @var list<array{hash: string}> */
        public array $parents = [],
    ) {}
}
