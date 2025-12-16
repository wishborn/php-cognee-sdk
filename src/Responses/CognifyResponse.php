<?php

declare(strict_types=1);

namespace Cognee\Responses;

/**
 * Response from cognifying (processing) data.
 */
readonly class CognifyResponse
{
    /**
     * @param bool $success Whether the operation was successful
     * @param string|null $message Response message
     * @param string|null $pipelineRunId Pipeline run ID for background processing
     * @param array<string, mixed> $data Response data
     */
    public function __construct(
        public bool $success,
        public ?string $message = null,
        public ?string $pipelineRunId = null,
        public array $data = [],
    ) {
    }

    /**
     * Create a CognifyResponse from an array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            success: $data['success'] ?? true,
            message: $data['message'] ?? null,
            pipelineRunId: $data['pipeline_run_id'] ?? $data['pipelineRunId'] ?? null,
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
            'pipeline_run_id' => $this->pipelineRunId,
            'data' => $this->data,
        ];
    }
}
