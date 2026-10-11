<?php

declare(strict_types=1);

namespace Tobiasn\BitbucketApi\DataTransferObjects;

use Spatie\LaravelData\Data;

final class CommentContentDTO extends Data
{
    public function __construct(
        public string $raw,
        public ?string $markup = null,
        public ?string $html = null,
    ) {}
}
