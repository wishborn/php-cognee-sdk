<?php

declare(strict_types=1);

namespace Cognee\Resources;

use Cognee\Exceptions\AuthenticationException;
use Cognee\Exceptions\CogneeException;
use Cognee\Exceptions\NotFoundException;
use Cognee\Exceptions\RateLimitException;
use Cognee\Exceptions\ServerException;
use Cognee\Exceptions\ValidationException;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

/**
 * Base class for all API resource classes.
 */
abstract class AbstractResource
{
    public function __construct(
        protected readonly ClientInterface $client,
    ) {
    }

    /**
     * Perform a GET request.
     *
     * @param string $uri Request URI
     * @param array<string, mixed> $options Request options
     * @return array<string, mixed> Response data
     * @throws CogneeException
     */
    protected function get(string $uri, array $options = []): array
    {
        return $this->request('GET', $uri, $options);
    }

    /**
     * Perform a POST request.
     *
     * @param string $uri Request URI
     * @param array<string, mixed> $data Request body data
     * @param array<string, mixed> $options Additional request options
     * @return array<string, mixed> Response data
     * @throws CogneeException
     */
    protected function post(string $uri, array $data = [], array $options = []): array
    {
        $options['json'] = $data;

        return $this->request('POST', $uri, $options);
    }

    /**
     * Perform a PUT request.
     *
     * @param string $uri Request URI
     * @param array<string, mixed> $data Request body data
     * @param array<string, mixed> $options Additional request options
     * @return array<string, mixed> Response data
     * @throws CogneeException
     */
    protected function put(string $uri, array $data = [], array $options = []): array
    {
        $options['json'] = $data;

        return $this->request('PUT', $uri, $options);
    }

    /**
     * Perform a DELETE request.
     *
     * @param string $uri Request URI
     * @param array<string, mixed> $options Request options
     * @return array<string, mixed> Response data
     * @throws CogneeException
     */
    protected function delete(string $uri, array $options = []): array
    {
        return $this->request('DELETE', $uri, $options);
    }

    /**
     * Perform an HTTP request.
     *
     * @param string $method HTTP method
     * @param string $uri Request URI
     * @param array<string, mixed> $options Request options
     * @return array<string, mixed> Response data
     * @throws CogneeException
     */
    protected function request(string $method, string $uri, array $options = []): array
    {
        try {
            $response = $this->client->request($method, $uri, $options);

            return $this->parseResponse($response);
        } catch (GuzzleException $e) {
            $response = method_exists($e, 'getResponse') ? $e->getResponse() : null;
            $request = method_exists($e, 'getRequest') ? $e->getRequest() : null;

            if ($response !== null) {
                $this->handleErrorResponse($response, $request);
            }

            throw new CogneeException(
                message: 'Request failed: ' . $e->getMessage(),
                code: $e->getCode(),
                request: $request,
            );
        }
    }

    /**
     * Parse the HTTP response.
     *
     * @param ResponseInterface $response HTTP response
     * @return array<string, mixed> Parsed response data
     */
    protected function parseResponse(ResponseInterface $response): array
    {
        $body = (string) $response->getBody();

        if (empty($body)) {
            return [];
        }

        $data = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return ['data' => $body];
        }

        return $data;
    }

    /**
     * Handle error responses and throw appropriate exceptions.
     *
     * @param ResponseInterface $response HTTP response
     * @param mixed $request HTTP request
     * @throws CogneeException
     */
    protected function handleErrorResponse(ResponseInterface $response, mixed $request): void
    {
        $statusCode = $response->getStatusCode();
        $body = (string) $response->getBody();
        $data = json_decode($body, true);
        $message = $data['message'] ?? $data['error'] ?? 'Request failed';

        $exception = match (true) {
            $statusCode === 401 => new AuthenticationException(
                message: $message,
                code: $statusCode,
                response: $response,
                request: $request,
            ),
            $statusCode === 404 => new NotFoundException(
                message: $message,
                code: $statusCode,
                response: $response,
                request: $request,
            ),
            $statusCode === 429 => new RateLimitException(
                message: $message,
                code: $statusCode,
                response: $response,
                request: $request,
            ),
            $statusCode >= 400 && $statusCode < 500 => new ValidationException(
                message: $message,
                code: $statusCode,
                response: $response,
                request: $request,
            ),
            $statusCode >= 500 => new ServerException(
                message: $message,
                code: $statusCode,
                response: $response,
                request: $request,
            ),
            default => new CogneeException(
                message: $message,
                code: $statusCode,
                response: $response,
                request: $request,
            ),
        };

        throw $exception;
    }
}
