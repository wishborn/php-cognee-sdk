<?php

declare(strict_types=1);

namespace Cognee;

use Cognee\Resources\Auth;
use Cognee\Resources\Datasets;
use Cognee\Resources\Health;
use Cognee\Resources\Permissions;
use Cognee\Resources\Search;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Psr\Http\Message\RequestInterface;

/**
 * Main Cognee SDK client.
 */
class Client
{
    private readonly ClientInterface $httpClient;

    private ?Datasets $datasets = null;

    private ?Search $search = null;

    private ?Auth $auth = null;

    private ?Permissions $permissions = null;

    private ?Health $health = null;

    public function __construct(
        private readonly Config $config,
    ) {
        $this->httpClient = $this->createHttpClient();
    }

    /**
     * Get the Datasets resource.
     */
    public function datasets(): Datasets
    {
        return $this->datasets ??= new Datasets($this->httpClient);
    }

    /**
     * Get the Search resource.
     */
    public function search(): Search
    {
        return $this->search ??= new Search($this->httpClient);
    }

    /**
     * Get the Auth resource.
     */
    public function auth(): Auth
    {
        return $this->auth ??= new Auth($this->httpClient);
    }

    /**
     * Get the Permissions resource.
     */
    public function permissions(): Permissions
    {
        return $this->permissions ??= new Permissions($this->httpClient);
    }

    /**
     * Get the Health resource.
     */
    public function health(): Health
    {
        return $this->health ??= new Health($this->httpClient);
    }

    /**
     * Create the HTTP client with configuration.
     */
    private function createHttpClient(): ClientInterface
    {
        $stack = HandlerStack::create();

        // Add authorization header middleware
        $stack->push(Middleware::mapRequest(function (RequestInterface $request) {
            return $request->withHeader('Authorization', 'Bearer ' . $this->config->apiKey);
        }));

        // Add retry middleware
        $stack->push(Middleware::retry(
            function (int $retries, RequestInterface $request, ?object $response = null, ?object $exception = null) {
                // Don't retry if we've exceeded max attempts
                if ($retries >= $this->config->retryAttempts) {
                    return false;
                }

                // Retry on server errors (5xx) or connection errors
                if ($exception !== null) {
                    return true;
                }

                if ($response !== null && method_exists($response, 'getStatusCode')) {
                    $statusCode = $response->getStatusCode();

                    return $statusCode >= 500 || $statusCode === 429;
                }

                return false;
            },
            function (int $retries) {
                // Exponential backoff: 1s, 2s, 4s, 8s...
                return 1000 * (2 ** $retries);
            },
        ));

        return new GuzzleClient([
            'base_uri' => rtrim($this->config->baseUrl, '/') . '/',
            'timeout' => $this->config->timeout,
            'handler' => $stack,
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    /**
     * Get the configuration.
     */
    public function getConfig(): Config
    {
        return $this->config;
    }

    /**
     * Get the HTTP client.
     */
    public function getHttpClient(): ClientInterface
    {
        return $this->httpClient;
    }
}
