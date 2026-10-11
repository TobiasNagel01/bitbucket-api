<?php

declare(strict_types=1);

namespace Tobiasn\BitbucketApi\Enums;

/**
 * Result of a completed pipeline or pipeline step.
 */
enum PipelineResult: string
{
    case SUCCESSFUL = 'SUCCESSFUL';
    case FAILED = 'FAILED';
    case ERROR = 'ERROR';
    case STOPPED = 'STOPPED';
    case EXPIRED = 'EXPIRED';
    case PAUSED = 'PAUSED';
    case HALTED = 'HALTED';
    case NOT_RUN = 'NOT_RUN';
}
