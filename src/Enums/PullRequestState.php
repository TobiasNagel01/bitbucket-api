<?php

declare(strict_types=1);

namespace Tobiasn\BitbucketApi\Enums;

enum PullRequestState: string
{
    case OPEN = 'OPEN';
    case MERGED = 'MERGED';
    case DECLINED = 'DECLINED';
    case SUPERSEDED = 'SUPERSEDED';
}
