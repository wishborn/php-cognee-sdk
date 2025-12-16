<?php

declare(strict_types=1);

namespace Cognee\Requests;

/**
 * Request for adding data to a dataset.
 */
readonly class AddDataRequest
{
    /**
     * @param mixed $data Data to add (string, array, file path)
     * @param string|null $datasetName Dataset name
     * @param string|null $datasetId Dataset ID
     */
    public function __construct(
        public mixed $data,
        public ?string $datasetName = null,
        public ?string $datasetId = null,
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
            'data' => $this->data,
        ];

        if ($this->datasetName !== null) {
            $payload['datasetName'] = $this->datasetName;
        }

        if ($this->datasetId !== null) {
            $payload['datasetId'] = $this->datasetId;
        }

        return $payload;
    }
}
