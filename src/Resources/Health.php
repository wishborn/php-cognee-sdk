<?php

declare(strict_types=1);

namespace Cognee\Resources;

/**
 * Health resource for checking API health status.
 */
class Health extends AbstractResource
{
    /**
     * Perform a basic health check.
     *
     * @return array<string, mixed>
     * @throws \Cognee\Exceptions\CogneeException
     */
    public function check(): array
    {
        return $this->get('health');
    }

    /**
     * Get detailed health status.
     *
     * @return array<string, mixed>
     * @throws \Cognee\Exceptions\CogneeException
     */
    public function detailed(): array
    {
        return $this->get('health/detailed');
    }
}
