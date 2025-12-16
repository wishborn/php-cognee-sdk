<?php

declare(strict_types=1);

namespace Cognee\Tests\Unit;

use Cognee\Client;
use Cognee\Config;
use Cognee\Resources\Auth;
use Cognee\Resources\Datasets;
use Cognee\Resources\Health;
use Cognee\Resources\Permissions;
use Cognee\Resources\Search;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    private Client $client;

    private Config $config;

    protected function setUp(): void
    {
        $this->config = new Config(
            baseUrl: 'https://api.example.com',
            apiKey: 'test-key',
        );

        $this->client = new Client($this->config);
    }

    public function testDatasetsReturnsDatasets(): void
    {
        $datasets = $this->client->datasets();

        $this->assertInstanceOf(Datasets::class, $datasets);
        $this->assertSame($datasets, $this->client->datasets());
    }

    public function testSearchReturnsSearch(): void
    {
        $search = $this->client->search();

        $this->assertInstanceOf(Search::class, $search);
        $this->assertSame($search, $this->client->search());
    }

    public function testAuthReturnsAuth(): void
    {
        $auth = $this->client->auth();

        $this->assertInstanceOf(Auth::class, $auth);
        $this->assertSame($auth, $this->client->auth());
    }

    public function testPermissionsReturnsPermissions(): void
    {
        $permissions = $this->client->permissions();

        $this->assertInstanceOf(Permissions::class, $permissions);
        $this->assertSame($permissions, $this->client->permissions());
    }

    public function testHealthReturnsHealth(): void
    {
        $health = $this->client->health();

        $this->assertInstanceOf(Health::class, $health);
        $this->assertSame($health, $this->client->health());
    }

    public function testGetConfigReturnsConfig(): void
    {
        $config = $this->client->getConfig();

        $this->assertSame($this->config, $config);
    }

    public function testGetHttpClientReturnsClient(): void
    {
        $httpClient = $this->client->getHttpClient();

        $this->assertNotNull($httpClient);
    }
}
