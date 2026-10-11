<?php

declare(strict_types=1);

namespace Tobiasn\BitbucketApi\DataTransferObjects;

use Spatie\LaravelData\Data;

final class UserDTO extends Data
{
    public function __construct(
        public string $uuid,
        public string $username,
        public string $account_id,
        public string $nickname,
        public string $account_status,
        public bool $has_2fa_enabled,
    ) {}
}
