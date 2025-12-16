<?php

declare(strict_types=1);

namespace Cognee\Tests\Unit\Resources;

use Cognee\Resources\Health;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class HealthTest extends TestCase
{
    private function createMockClient(array $responses): Client
    {
        $mock = new MockHandler($responses);
        $handlerStack = HandlerStack::create($mock);

        return new Client(['handler' => $handlerStack]);
    }

    public function testCheckReturnsHealthStatus(): void
    {
        $mockResponse = new Response(200, [], json_encode([
            'status' => 'healthy',
            'timestamp' => '2024-01-01T00:00:00Z',
        ]));

        $client = $this->createMockClient([$mockResponse]);
        $health = new Health($client);

        $result = $health->check();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('status', $result);
        $this->assertSame('healthy', $result['status']);
    }

    public function testDetailedReturnsDetailedHealthStatus(): void
    {
        $mockResponse = new Response(200, [], json_encode([
            'status' => 'healthy',
            'services' => [
                'database' => 'up',
                'vector_store' => 'up',
                'graph_db' => 'up',
            ],
            'timestamp' => '2024-01-01T00:00:00Z',
        ]));

        $client = $this->createMockClient([$mockResponse]);
        $health = new Health($client);

        $result = $health->detailed();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('status', $result);
        $this->assertArrayHasKey('services', $result);
        $this->assertSame('healthy', $result['status']);
    }
}
