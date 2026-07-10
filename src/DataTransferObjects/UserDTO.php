<?php

namespace Tobiasn\BitbucketApi\DataTransferObjects;

final class UserDTO
{
    public function __construct(
        private string $uuid,
        private string $username,
        private string $account_id,
        private string $nickname,
        private string $account_status,
        private bool   $has_2fa_enabled,
    )
    {
    }
}