<?php

declare(strict_types=1);

namespace Cognee\Tests\Unit\Resources;

use Cognee\Resources\Permissions;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class PermissionsTest extends TestCase
{
    private function createMockClient(array $responses): Client
    {
        $mock = new MockHandler($responses);
        $handlerStack = HandlerStack::create($mock);

        return new Client(['handler' => $handlerStack]);
    }

    public function testGrantDatasetPermissionReturnsTrue(): void
    {
        $mockResponse = new Response(200, [], json_encode(['success' => true]));

        $client = $this->createMockClient([$mockResponse]);
        $permissions = new Permissions($client);

        $result = $permissions->grantDatasetPermission('user-1', [
            'read' => true,
            'write' => true,
        ]);

        $this->assertTrue($result);
    }

    public function testCreateRoleReturnsArray(): void
    {
        $mockResponse = new Response(200, [], json_encode([
            'role' => [
                'id' => 'role-1',
                'name' => 'Editor',
                'permissions' => ['read', 'write'],
            ],
        ]));

        $client = $this->createMockClient([$mockResponse]);
        $permissions = new Permissions($client);

        $result = $permissions->createRole([
            'name' => 'Editor',
            'permissions' => ['read', 'write'],
        ]);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('role', $result);
    }

    public function testAssignUserToRoleReturnsTrue(): void
    {
        $mockResponse = new Response(200, [], json_encode(['success' => true]));

        $client = $this->createMockClient([$mockResponse]);
        $permissions = new Permissions($client);

        $result = $permissions->assignUserToRole('user-1', 'role-1');

        $this->assertTrue($result);
    }

    public function testCreateTenantReturnsArray(): void
    {
        $mockResponse = new Response(200, [], json_encode([
            'tenant' => [
                'id' => 'tenant-1',
                'name' => 'Acme Corp',
            ],
        ]));

        $client = $this->createMockClient([$mockResponse]);
        $permissions = new Permissions($client);

        $result = $permissions->createTenant([
            'name' => 'Acme Corp',
        ]);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('tenant', $result);
    }
}
