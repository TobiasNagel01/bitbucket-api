<?php

declare(strict_types=1);

namespace Tobiasn\BitbucketApi\Enums;

enum ForkPolicy: string
{
    case ALLOW_FORKS = 'allow_forks';
    case NO_PUBLIC_FORKS = 'no_public_forks';
    case NO_FORKS = 'no_forks';
}
