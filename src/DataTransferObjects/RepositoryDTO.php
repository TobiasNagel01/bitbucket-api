<?php

namespace Tobiasn\BitbucketApi\DataTransferObjects;

use Spatie\LaravelData\Data;

final class RepositoryDTO extends Data
{
    public function __construct(
        public string     $uuid,
        public string     $full_name,
        public string     $is_private,
        public AccountDTO $owner,
        public string     $name,
        public string     $description,
    )
    {
    }
}