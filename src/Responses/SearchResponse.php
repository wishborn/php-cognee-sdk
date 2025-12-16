<?php

declare(strict_types=1);

namespace Cognee\Responses;

use Cognee\Models\SearchResult;

/**
 * Response from a search query.
 */
readonly class SearchResponse
{
    /**
     * @param array<SearchResult> $results Search results
     * @param int $total Total number of results
     */
    public function __construct(
        public array $results,
        public int $total = 0,
    ) {
    }

    /**
     * Create a SearchResponse from an array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $results = [];
        $rawResults = $data['results'] ?? $data;

        if (is_array($rawResults)) {
            foreach ($rawResults as $result) {
                if (is_array($result)) {
                    $results[] = SearchResult::fromArray($result);
                }
            }
        }

        return new self(
            results: $results,
            total: $data['total'] ?? count($results),
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
            'results' => array_map(fn (SearchResult $result) => $result->toArray(), $this->results),
            'total' => $this->total,
        ];
    }
}
