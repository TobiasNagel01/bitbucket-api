<?php

declare(strict_types=1);

namespace Tobiasn\BitbucketApi\DataTransferObjects;

use Spatie\LaravelData\Data;

final class WorkspaceDTO extends Data
{
    public function __construct(
        public string $uuid,
        public string $slug,
        public string $name,
        public bool $is_private = false,
    ) {}
}
