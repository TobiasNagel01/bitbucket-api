<?php

declare(strict_types=1);

namespace Tobiasn\BitbucketApi\Enums;

/**
 * State of a pipeline or pipeline step.
 */
enum PipelineState: string
{
    case PENDING = 'PENDING';
    case READY = 'READY';
    case IN_PROGRESS = 'IN_PROGRESS';
    case COMPLETED = 'COMPLETED';
    case PAUSED = 'PAUSED';
    case HALTED = 'HALTED';
    case NOT_RUN = 'NOT_RUN';
}
