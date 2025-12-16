<?php

declare(strict_types=1);

namespace Cognee\Tests\Unit\Exceptions;

use Cognee\Exceptions\AuthenticationException;
use Cognee\Exceptions\CogneeException;
use Cognee\Exceptions\NotFoundException;
use Cognee\Exceptions\RateLimitException;
use Cognee\Exceptions\ServerException;
use Cognee\Exceptions\ValidationException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class CogneeExceptionTest extends TestCase
{
    public function testExceptionWithResponse(): void
    {
        $response = new Response(404, [], 'Not found');
        $request = new Request('GET', '/test');

        $exception = new CogneeException(
            message: 'Resource not found',
            code: 404,
            response: $response,
            request: $request,
        );

        $this->assertSame('Resource not found', $exception->getMessage());
        $this->assertSame(404, $exception->getCode());
        $this->assertSame($response, $exception->getResponse());
        $this->assertSame($request, $exception->getRequest());
        $this->assertSame('Not found', $exception->getResponseBody());
    }

    public function testAuthenticationExceptionExtendsBaseException(): void
    {
        $exception = new AuthenticationException('Unauthorized', 401);

        $this->assertInstanceOf(CogneeException::class, $exception);
        $this->assertSame(401, $exception->getCode());
    }

    public function testValidationExceptionExtendsBaseException(): void
    {
        $exception = new ValidationException('Invalid data', 400);

        $this->assertInstanceOf(CogneeException::class, $exception);
    }

    public function testNotFoundExceptionExtendsBaseException(): void
    {
        $exception = new NotFoundException('Not found', 404);

        $this->assertInstanceOf(CogneeException::class, $exception);
    }

    public function testRateLimitExceptionExtendsBaseException(): void
    {
        $exception = new RateLimitException('Too many requests', 429);

        $this->assertInstanceOf(CogneeException::class, $exception);
    }

    public function testServerExceptionExtendsBaseException(): void
    {
        $exception = new ServerException('Server error', 500);

        $this->assertInstanceOf(CogneeException::class, $exception);
    }
}
