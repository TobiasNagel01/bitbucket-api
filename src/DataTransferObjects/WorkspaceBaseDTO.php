<?php

declare(strict_types=1);

namespace Tobiasn\BitbucketApi\DataTransferObjects;

use Spatie\LaravelData\Data;

/**
 * Minimal workspace representation (`workspace_base`) as embedded in
 * `/2.0/user/workspaces` responses. Only `uuid` and `slug` are guaranteed;
 * load the full workspace via `GetWorkspaceRequest` when more detail is needed.
 */
final class WorkspaceBaseDTO extends Data
{
    public function __construct(
        public string $uuid,
        public string $slug,
        public ?string $name = null,
    ) {}
}
