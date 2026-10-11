<?php

declare(strict_types=1);

namespace Tobiasn\BitbucketApi\DataTransferObjects;

use Spatie\LaravelData\Data;

final class DiffstatDTO extends Data
{
    public function __construct(
        public string $status,
        public int $lines_added,
        public int $lines_removed,
        public ?DiffstatFileDTO $old = null,
        public ?DiffstatFileDTO $new = null,
    ) {}
}
