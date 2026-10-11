<?php

declare(strict_types=1);

namespace Tobiasn\BitbucketApi\DataTransferObjects;

use Spatie\LaravelData\Data;

final class RepositoryDTO extends Data
{
    public function __construct(
        public string $uuid,
        public string $full_name,
        public string $slug,
        public bool $is_private,
        public AccountDTO $owner,
        public string $name,
        public ?string $description = null,
        public ?string $language = null,
    ) {}
}
