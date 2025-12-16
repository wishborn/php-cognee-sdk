<?php

declare(strict_types=1);

namespace Cognee\Requests;

/**
 * Request for cognifying (processing) data into knowledge graphs.
 */
readonly class CognifyRequest
{
    /**
     * @param array<string>|null $datasets Dataset names to process
     * @param array<string>|null $datasetIds Dataset IDs to process
     * @param bool $runInBackground Whether to run processing in background
     */
    public function __construct(
        public ?array $datasets = null,
        public ?array $datasetIds = null,
        public bool $runInBackground = false,
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
            'run_in_background' => $this->runInBackground,
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
