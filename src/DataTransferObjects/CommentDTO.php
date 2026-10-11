<?php

declare(strict_types=1);

namespace Tobiasn\BitbucketApi\DataTransferObjects;

use Spatie\LaravelData\Data;

final class CommentDTO extends Data
{
    public function __construct(
        public int $id,
        public CommentContentDTO $content,
        public AccountDTO $user,
        public ?CommentInlineDTO $inline = null,
        public ?CommentParentDTO $parent = null,
        public bool $deleted = false,
        public ?string $created_on = null,
        public ?string $updated_on = null,
    ) {}
}
