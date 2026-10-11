<?php

declare(strict_types=1);

namespace Tobiasn\BitbucketApi\DataTransferObjects;

use Spatie\LaravelData\Data;

final class ActivityDTO extends Data
{
    public function __construct(
        /** @var array<string, mixed>|null */
        public ?array $update = null,
        /** @var array<string, mixed>|null */
        public ?array $approval = null,
        /** @var array<string, mixed>|null */
        public ?array $comment = null,
        /** @var array<string, mixed>|null */
        public ?array $changes_requested = null,
    ) {}
}
