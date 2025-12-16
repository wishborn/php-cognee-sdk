<?php

declare(strict_types=1);

namespace Cognee\Enums;

/**
 * Dataset processing status enumeration.
 */
enum DatasetStatus: string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case COMPLETED = 'completed';
    case FAILED = 'failed';
}
