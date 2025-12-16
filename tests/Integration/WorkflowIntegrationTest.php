<?php

declare(strict_types=1);

namespace Cognee\Tests\Integration;

use Cognee\Client;
use Cognee\Config;
use Cognee\Enums\SearchType;
use Cognee\Requests\AddDataRequest;
use Cognee\Requests\CognifyRequest;
use Cognee\Requests\SearchRequest;
use PHPUnit\Framework\TestCase;

/**
 * Integration test for the complete workflow: add -> cognify -> search.
 *
 * Note: These tests require a running Cognee instance.
 * Set COGNEE_BASE_URL and COGNEE_API_KEY environment variables.
 */
class WorkflowIntegrationTest extends TestCase
{
    private Client $client;

    protected function setUp(): void
    {
        $baseUrl = getenv('COGNEE_BASE_URL');
        $apiKey = getenv('COGNEE_API_KEY');

        if (!$baseUrl || !$apiKey) {
            $this->markTestSkipped('Integration tests require COGNEE_BASE_URL and COGNEE_API_KEY environment variables');
        }

        $config = new Config($baseUrl, $apiKey);
        $this->client = new Client($config);
    }

    public function testHealthCheck(): void
    {
        $health = $this->client->health()->check();

        $this->assertIsArray($health);
        $this->assertArrayHasKey('status', $health);
    }

    public function testCompleteWorkflow(): void
    {
        // 1. Create a dataset
        $dataset = $this->client->datasets()->create('integration-test-' . time());
        $this->assertNotEmpty($dataset->id);

        // 2. Add data to the dataset
        $addRequest = new AddDataRequest(
            data: 'The quick brown fox jumps over the lazy dog. This is a test document for Cognee.',
            datasetId: $dataset->id,
        );

        $addResponse = $this->client->datasets()->add($addRequest);
        $this->assertTrue($addResponse->success);

        // 3. Cognify the data
        $cognifyRequest = new CognifyRequest(
            datasetIds: [$dataset->id],
            runInBackground: false,
        );

        $cognifyResponse = $this->client->datasets()->cognify($cognifyRequest);
        $this->assertTrue($cognifyResponse->success);

        // 4. Search the knowledge graph
        $searchRequest = new SearchRequest(
            query: 'fox',
            searchType: SearchType::SEMANTIC,
            datasetIds: [$dataset->id],
            topK: 5,
        );

        $searchResponse = $this->client->search()->search($searchRequest);
        $this->assertNotEmpty($searchResponse->results);

        // 5. Clean up: delete the dataset
        $deleted = $this->client->datasets()->deleteDataset($dataset->id);
        $this->assertTrue($deleted);
    }

    public function testListDatasets(): void
    {
        $datasets = $this->client->datasets()->list();

        $this->assertIsArray($datasets);
    }
}
