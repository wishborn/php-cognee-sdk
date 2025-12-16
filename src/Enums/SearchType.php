<?php

declare(strict_types=1);

namespace Cognee\Enums;

/**
 * Search type enumeration.
 */
enum SearchType: string
{
    case SEMANTIC = 'semantic';
    case KEYWORD = 'keyword';
    case HYBRID = 'hybrid';
}
