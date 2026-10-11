<?php

declare(strict_types=1);

namespace Tobiasn\BitbucketApi\DataTransferObjects;

use Spatie\LaravelData\Data;

final class AccountDTO extends Data
{
    public function __construct(
        public string $uuid,
        public string $display_name,
        public ?string $nickname = null,
        public ?string $account_id = null,
        public ?string $type = null,
    ) {}
}
