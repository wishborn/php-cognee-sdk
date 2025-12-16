<?php

declare(strict_types=1);

namespace Cognee\Models;

/**
 * Search result model.
 */
readonly class SearchResult
{
    /**
     * @param string $id Result ID
     * @param string $text Result text content
     * @param float $score Relevance score
     * @param array<string, mixed> $metadata Result metadata
     * @param string|null $datasetId Dataset ID this result belongs to
     */
    public function __construct(
        public string $id,
        public string $text,
        public float $score,
        public array $metadata = [],
        public ?string $datasetId = null,
    ) {
    }

    /**
     * Create a SearchResult from an array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? '',
            text: $data['text'] ?? $data['content'] ?? '',
            score: (float) ($data['score'] ?? $data['similarity'] ?? 0.0),
            metadata: $data['metadata'] ?? [],
            datasetId: $data['dataset_id'] ?? $data['datasetId'] ?? null,
        );
    }

    /**
     * Convert the SearchResult to an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'text' => $this->text,
            'score' => $this->score,
            'metadata' => $this->metadata,
            'dataset_id' => $this->datasetId,
        ];
    }
}
