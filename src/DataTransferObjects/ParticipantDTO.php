<?php

declare(strict_types=1);

namespace Tobiasn\BitbucketApi\DataTransferObjects;

use Spatie\LaravelData\Data;

final class ParticipantDTO extends Data
{
    public function __construct(
        public AccountDTO $user,
        public string $role,
        public bool $approved = false,
        public ?string $state = null,
        public ?string $participated_on = null,
    ) {}
}
