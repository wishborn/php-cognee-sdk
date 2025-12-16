<?php

declare(strict_types=1);

namespace Cognee\Tests\Unit;

use Cognee\Config;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    public function testConstructorCreatesValidConfig(): void
    {
        $config = new Config(
            baseUrl: 'https://api.example.com',
            apiKey: 'test-key',
            timeout: 60,
            retryAttempts: 5,
        );

        $this->assertSame('https://api.example.com', $config->baseUrl);
        $this->assertSame('test-key', $config->apiKey);
        $this->assertSame(60, $config->timeout);
        $this->assertSame(5, $config->retryAttempts);
    }

    public function testConstructorWithDefaults(): void
    {
        $config = new Config(
            baseUrl: 'https://api.example.com',
            apiKey: 'test-key',
        );

        $this->assertSame(30, $config->timeout);
        $this->assertSame(3, $config->retryAttempts);
    }

    public function testThrowsExceptionForEmptyBaseUrl(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Base URL cannot be empty');

        new Config(baseUrl: '', apiKey: 'test-key');
    }

    public function testThrowsExceptionForInvalidBaseUrl(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Base URL must be a valid URL');

        new Config(baseUrl: 'not-a-url', apiKey: 'test-key');
    }

    public function testThrowsExceptionForEmptyApiKey(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('API key cannot be empty');

        new Config(baseUrl: 'https://api.example.com', apiKey: '');
    }

    public function testThrowsExceptionForInvalidTimeout(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Timeout must be at least 1 second');

        new Config(baseUrl: 'https://api.example.com', apiKey: 'test-key', timeout: 0);
    }

    public function testThrowsExceptionForNegativeRetryAttempts(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Retry attempts cannot be negative');

        new Config(baseUrl: 'https://api.example.com', apiKey: 'test-key', retryAttempts: -1);
    }

    public function testWithCreatesNewInstanceWithUpdatedValues(): void
    {
        $config = new Config(
            baseUrl: 'https://api.example.com',
            apiKey: 'test-key',
        );

        $newConfig = $config->with(['timeout' => 60]);

        $this->assertSame(30, $config->timeout);
        $this->assertSame(60, $newConfig->timeout);
        $this->assertSame('https://api.example.com', $newConfig->baseUrl);
        $this->assertSame('test-key', $newConfig->apiKey);
    }
}
