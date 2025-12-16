<?php

declare(strict_types=1);

namespace Cognee\Tests\Unit\Resources;

use Cognee\Models\User;
use Cognee\Resources\Auth;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class AuthTest extends TestCase
{
    private function createMockClient(array $responses): Client
    {
        $mock = new MockHandler($responses);
        $handlerStack = HandlerStack::create($mock);

        return new Client(['handler' => $handlerStack]);
    }

    public function testLoginReturnsUser(): void
    {
        $mockResponse = new Response(200, [], json_encode([
            'user' => [
                'id' => '1',
                'email' => 'test@example.com',
                'name' => 'Test User',
            ],
        ]));

        $client = $this->createMockClient([$mockResponse]);
        $auth = new Auth($client);

        $result = $auth->login('test@example.com', 'password');

        $this->assertInstanceOf(User::class, $result);
        $this->assertSame('1', $result->id);
        $this->assertSame('test@example.com', $result->email);
        $this->assertSame('Test User', $result->name);
    }

    public function testLogoutReturnsTrue(): void
    {
        $mockResponse = new Response(200, [], json_encode(['success' => true]));

        $client = $this->createMockClient([$mockResponse]);
        $auth = new Auth($client);

        $result = $auth->logout();

        $this->assertTrue($result);
    }

    public function testRegisterReturnsUser(): void
    {
        $mockResponse = new Response(200, [], json_encode([
            'user' => [
                'id' => '2',
                'email' => 'new@example.com',
                'name' => 'New User',
            ],
        ]));

        $client = $this->createMockClient([$mockResponse]);
        $auth = new Auth($client);

        $result = $auth->register([
            'email' => 'new@example.com',
            'password' => 'password123',
            'name' => 'New User',
        ]);

        $this->assertInstanceOf(User::class, $result);
        $this->assertSame('2', $result->id);
        $this->assertSame('new@example.com', $result->email);
    }

    public function testForgotPasswordReturnsTrue(): void
    {
        $mockResponse = new Response(200, [], json_encode(['success' => true]));

        $client = $this->createMockClient([$mockResponse]);
        $auth = new Auth($client);

        $result = $auth->forgotPassword('test@example.com');

        $this->assertTrue($result);
    }

    public function testVerifyReturnsTrue(): void
    {
        $mockResponse = new Response(200, [], json_encode(['success' => true]));

        $client = $this->createMockClient([$mockResponse]);
        $auth = new Auth($client);

        $result = $auth->verify('verification-token');

        $this->assertTrue($result);
    }
}
