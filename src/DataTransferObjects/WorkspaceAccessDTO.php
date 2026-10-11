<?php

declare(strict_types=1);

namespace Tobiasn\BitbucketApi\DataTransferObjects;

use Spatie\LaravelData\Data;

/**
 * A workspace the authenticated user has access to (`workspace_access`),
 * including whether the user administers it.
 */
final class WorkspaceAccessDTO extends Data
{
    public function __construct(
        public bool $administrator,
        public WorkspaceBaseDTO $workspace,
    ) {}
}
