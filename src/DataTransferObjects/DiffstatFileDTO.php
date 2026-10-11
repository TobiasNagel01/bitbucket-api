<?php

declare(strict_types=1);

namespace Tobiasn\BitbucketApi\DataTransferObjects;

use Spatie\LaravelData\Data;

final class DiffstatFileDTO extends Data
{
    public function __construct(
        public string $path,
        public ?string $type = null,
    ) {}
}
