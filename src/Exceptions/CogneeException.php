<?php

declare(strict_types=1);

namespace Cognee\Exceptions;

use Exception;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Base exception for all Cognee SDK exceptions.
 */
class CogneeException extends Exception
{
    /**
     * @param string $message Error message
     * @param int $code HTTP status code
     * @param ResponseInterface|null $response HTTP response
     * @param RequestInterface|null $request HTTP request
     */
    public function __construct(
        string $message,
        int $code = 0,
        private readonly ?ResponseInterface $response = null,
        private readonly ?RequestInterface $request = null,
    ) {
        parent::__construct($message, $code);
    }

    public function getResponse(): ?ResponseInterface
    {
        return $this->response;
    }

    public function getRequest(): ?RequestInterface
    {
        return $this->request;
    }

    /**
     * Get the response body as a string.
     */
    public function getResponseBody(): ?string
    {
        if ($this->response === null) {
            return null;
        }

        return (string) $this->response->getBody();
    }
}
