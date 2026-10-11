<?php

declare(strict_types=1);

namespace Tobiasn\BitbucketApi\Enums;

enum MergeStrategy: string
{
    case MERGE_COMMIT = 'merge_commit';
    case SQUASH = 'squash';
    case FAST_FORWARD = 'fast_forward';
}
