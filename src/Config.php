<?php

declare(strict_types=1);

namespace Cognee;

use InvalidArgumentException;

/**
 * Immutable configuration class for Cognee client.
 */
readonly class Config
{
    /**
     * @param string $baseUrl Base URL of the Cognee API
     * @param string $apiKey API key for authentication
     * @param int $timeout Request timeout in seconds
     * @param int $retryAttempts Number of retry attempts for failed requests
     */
    public function __construct(
        public string $baseUrl,
        public string $apiKey,
        public int $timeout = 30,
        public int $retryAttempts = 3,
    ) {
        $this->validate();
    }

    /**
     * Validate configuration values.
     *
     * @throws InvalidArgumentException
     */
    private function validate(): void
    {
        if (empty($this->baseUrl)) {
            throw new InvalidArgumentException('Base URL cannot be empty');
        }

        if (!filter_var($this->baseUrl, FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException('Base URL must be a valid URL');
        }

        if (empty($this->apiKey)) {
            throw new InvalidArgumentException('API key cannot be empty');
        }

        if ($this->timeout < 1) {
            throw new InvalidArgumentException('Timeout must be at least 1 second');
        }

        if ($this->retryAttempts < 0) {
            throw new InvalidArgumentException('Retry attempts cannot be negative');
        }
    }

    /**
     * Create a new configuration instance with updated values.
     *
     * @param array<string, mixed> $updates
     */
    public function with(array $updates): self
    {
        return new self(
            baseUrl: $updates['baseUrl'] ?? $this->baseUrl,
            apiKey: $updates['apiKey'] ?? $this->apiKey,
            timeout: $updates['timeout'] ?? $this->timeout,
            retryAttempts: $updates['retryAttempts'] ?? $this->retryAttempts,
        );
    }
}
