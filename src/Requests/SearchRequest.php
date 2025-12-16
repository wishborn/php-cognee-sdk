<?php

declare(strict_types=1);

namespace Cognee\Requests;

use Cognee\Enums\SearchType;

/**
 * Request for searching the knowledge graph.
 */
readonly class SearchRequest
{
    /**
     * @param string $query Search query
     * @param SearchType $searchType Type of search to perform
     * @param array<string>|null $datasets Dataset names to search in
     * @param array<string>|null $datasetIds Dataset IDs to search in
     * @param int $topK Number of top results to return
     */
    public function __construct(
        public string $query,
        public SearchType $searchType = SearchType::SEMANTIC,
        public ?array $datasets = null,
        public ?array $datasetIds = null,
        public int $topK = 10,
    ) {
    }

    /**
     * Convert the request to an array for API submission.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $payload = [
            'query' => $this->query,
            'search_type' => $this->searchType->value,
            'top_k' => $this->topK,
        ];

        if ($this->datasets !== null) {
            $payload['datasets'] = $this->datasets;
        }

        if ($this->datasetIds !== null) {
            $payload['dataset_ids'] = $this->datasetIds;
        }

        return $payload;
    }
}
