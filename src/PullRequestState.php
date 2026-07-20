<?php

namespace Tobiasn\BitbucketApi;

enum PullRequestState
{
    case OPEN;
    case MERGED;
    case DECLINED;
    case SUPERSEDED;
}
