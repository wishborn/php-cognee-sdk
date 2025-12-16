<?php

declare(strict_types=1);

namespace Cognee\Exceptions;

/**
 * Exception thrown when rate limit is exceeded (429).
 */
class RateLimitException extends CogneeException
{
}
