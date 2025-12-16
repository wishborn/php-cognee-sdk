<?php

declare(strict_types=1);

namespace Cognee\Resources;

use Cognee\Requests\SearchRequest;
use Cognee\Responses\SearchResponse;

/**
 * Search resource for querying the knowledge graph.
 */
class Search extends AbstractResource
{
    /**
     * Perform a search query.
     *
     * @param SearchRequest $request Search request
     * @return SearchResponse
     * @throws \Cognee\Exceptions\CogneeException
     */
    public function search(SearchRequest $request): SearchResponse
    {
        $response = $this->post('api/v1/search', $request->toArray());

        return SearchResponse::fromArray($response);
    }

    /**
     * Get search history for the authenticated user.
     *
     * @return array<array<string, mixed>>
     * @throws \Cognee\Exceptions\CogneeException
     */
    public function history(): array
    {
        $response = $this->get('api/v1/search');

        return $response['history'] ?? $response;
    }
}
