<?php

declare(strict_types=1);

namespace Cognee\Tests\Unit\Resources;

use Cognee\Models\Dataset;
use Cognee\Requests\AddDataRequest;
use Cognee\Requests\CognifyRequest;
use Cognee\Resources\Datasets;
use Cognee\Responses\AddDataResponse;
use Cognee\Responses\CognifyResponse;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class DatasetsTest extends TestCase
{
    private function createMockClient(array $responses): Client
    {
        $mock = new MockHandler($responses);
        $handlerStack = HandlerStack::create($mock);

        return new Client(['handler' => $handlerStack]);
    }

    public function testListReturnsArrayOfDatasets(): void
    {
        $mockResponse = new Response(200, [], json_encode([
            'datasets' => [
                ['id' => '1', 'name' => 'Dataset 1'],
                ['id' => '2', 'name' => 'Dataset 2'],
            ],
        ]));

        $client = $this->createMockClient([$mockResponse]);
        $datasets = new Datasets($client);

        $result = $datasets->list();

        $this->assertCount(2, $result);
        $this->assertInstanceOf(Dataset::class, $result[0]);
        $this->assertSame('1', $result[0]->id);
        $this->assertSame('Dataset 1', $result[0]->name);
    }

    public function testCreateReturnsDataset(): void
    {
        $mockResponse = new Response(200, [], json_encode([
            'dataset' => ['id' => '1', 'name' => 'New Dataset'],
        ]));

        $client = $this->createMockClient([$mockResponse]);
        $datasets = new Datasets($client);

        $result = $datasets->create('New Dataset', ['key' => 'value']);

        $this->assertInstanceOf(Dataset::class, $result);
        $this->assertSame('1', $result->id);
        $this->assertSame('New Dataset', $result->name);
    }

    public function testGetDatasetReturnsDataset(): void
    {
        $mockResponse = new Response(200, [], json_encode([
            'dataset' => ['id' => '1', 'name' => 'Test Dataset'],
        ]));

        $client = $this->createMockClient([$mockResponse]);
        $datasets = new Datasets($client);

        $result = $datasets->getDataset('1');

        $this->assertInstanceOf(Dataset::class, $result);
        $this->assertSame('1', $result->id);
        $this->assertSame('Test Dataset', $result->name);
    }

    public function testDeleteDatasetReturnsTrue(): void
    {
        $mockResponse = new Response(200, [], json_encode(['success' => true]));

        $client = $this->createMockClient([$mockResponse]);
        $datasets = new Datasets($client);

        $result = $datasets->deleteDataset('1');

        $this->assertTrue($result);
    }

    public function testGetGraphReturnsArray(): void
    {
        $mockResponse = new Response(200, [], json_encode([
            'nodes' => [['id' => '1', 'label' => 'Node 1']],
            'edges' => [['from' => '1', 'to' => '2']],
        ]));

        $client = $this->createMockClient([$mockResponse]);
        $datasets = new Datasets($client);

        $result = $datasets->getGraph('1');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('nodes', $result);
        $this->assertArrayHasKey('edges', $result);
    }

    public function testGetDataReturnsArray(): void
    {
        $mockResponse = new Response(200, [], json_encode([
            'data' => ['item1', 'item2'],
        ]));

        $client = $this->createMockClient([$mockResponse]);
        $datasets = new Datasets($client);

        $result = $datasets->getData('1');

        $this->assertIsArray($result);
    }

    public function testGetStatusReturnsArray(): void
    {
        $mockResponse = new Response(200, [], json_encode([
            'status' => 'completed',
            'progress' => 100,
        ]));

        $client = $this->createMockClient([$mockResponse]);
        $datasets = new Datasets($client);

        $result = $datasets->getStatus('1');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('status', $result);
    }

    public function testAddReturnsAddDataResponse(): void
    {
        $mockResponse = new Response(200, [], json_encode([
            'success' => true,
            'message' => 'Data added successfully',
        ]));

        $client = $this->createMockClient([$mockResponse]);
        $datasets = new Datasets($client);

        $request = new AddDataRequest('test data', 'test-dataset');
        $result = $datasets->add($request);

        $this->assertInstanceOf(AddDataResponse::class, $result);
        $this->assertTrue($result->success);
        $this->assertSame('Data added successfully', $result->message);
    }

    public function testCognifyReturnsCognifyResponse(): void
    {
        $mockResponse = new Response(200, [], json_encode([
            'success' => true,
            'message' => 'Processing started',
            'pipeline_run_id' => 'run-123',
        ]));

        $client = $this->createMockClient([$mockResponse]);
        $datasets = new Datasets($client);

        $request = new CognifyRequest(['test-dataset'], null, true);
        $result = $datasets->cognify($request);

        $this->assertInstanceOf(CognifyResponse::class, $result);
        $this->assertTrue($result->success);
        $this->assertSame('run-123', $result->pipelineRunId);
    }
}
