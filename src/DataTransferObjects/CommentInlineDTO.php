<?php

declare(strict_types=1);

namespace Tobiasn\BitbucketApi\DataTransferObjects;

use Spatie\LaravelData\Data;

final class CommentInlineDTO extends Data
{
    public function __construct(
        public string $path,
        public ?int $from = null,
        public ?int $to = null,
    ) {}
}
