<?php

declare(strict_types=1);

namespace Cognee\Responses;

/**
 * Response from adding data to a dataset.
 */
readonly class AddDataResponse
{
    /**
     * @param bool $success Whether the operation was successful
     * @param string|null $message Response message
     * @param array<string, mixed> $data Response data
     */
    public function __construct(
        public bool $success,
        public ?string $message = null,
        public array $data = [],
    ) {
    }

    /**
     * Create an AddDataResponse from an array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            success: $data['success'] ?? true,
            message: $data['message'] ?? null,
            data: $data['data'] ?? $data,
        );
    }

    /**
     * Convert the response to an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'message' => $this->message,
            'data' => $this->data,
        ];
    }
}
