<?php

declare(strict_types=1);

namespace Cognee\Tests\Unit\Resources;

use Cognee\Enums\SearchType;
use Cognee\Requests\SearchRequest;
use Cognee\Resources\Search;
use Cognee\Responses\SearchResponse;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class SearchTest extends TestCase
{
    private function createMockClient(array $responses): Client
    {
        $mock = new MockHandler($responses);
        $handlerStack = HandlerStack::create($mock);

        return new Client(['handler' => $handlerStack]);
    }

    public function testSearchReturnsSearchResponse(): void
    {
        $mockResponse = new Response(200, [], json_encode([
            'results' => [
                [
                    'id' => '1',
                    'text' => 'Result 1',
                    'score' => 0.95,
                    'metadata' => [],
                ],
                [
                    'id' => '2',
                    'text' => 'Result 2',
                    'score' => 0.85,
                    'metadata' => [],
                ],
            ],
            'total' => 2,
        ]));

        $client = $this->createMockClient([$mockResponse]);
        $search = new Search($client);

        $request = new SearchRequest('test query', SearchType::SEMANTIC, null, null, 10);
        $result = $search->search($request);

        $this->assertInstanceOf(SearchResponse::class, $result);
        $this->assertCount(2, $result->results);
        $this->assertSame(2, $result->total);
        $this->assertSame('Result 1', $result->results[0]->text);
        $this->assertSame(0.95, $result->results[0]->score);
    }

    public function testSearchWithDifferentSearchTypes(): void
    {
        $mockResponse = new Response(200, [], json_encode([
            'results' => [],
            'total' => 0,
        ]));

        $client = $this->createMockClient([$mockResponse, $mockResponse, $mockResponse]);
        $search = new Search($client);

        foreach ([SearchType::SEMANTIC, SearchType::KEYWORD, SearchType::HYBRID] as $type) {
            $request = new SearchRequest('test', $type);
            $result = $search->search($request);

            $this->assertInstanceOf(SearchResponse::class, $result);
        }
    }

    public function testHistoryReturnsArray(): void
    {
        $mockResponse = new Response(200, [], json_encode([
            'history' => [
                ['query' => 'query 1', 'timestamp' => '2024-01-01T00:00:00Z'],
                ['query' => 'query 2', 'timestamp' => '2024-01-02T00:00:00Z'],
            ],
        ]));

        $client = $this->createMockClient([$mockResponse]);
        $search = new Search($client);

        $result = $search->history();

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
    }
}
