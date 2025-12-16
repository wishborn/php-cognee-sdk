<?php

declare(strict_types=1);

namespace Cognee\Models;

/**
 * Dataset model.
 */
readonly class Dataset
{
    /**
     * @param string $id Dataset ID
     * @param string $name Dataset name
     * @param array<string, mixed>|null $metadata Dataset metadata
     * @param string|null $createdAt Creation timestamp
     * @param string|null $updatedAt Last update timestamp
     * @param string|null $ownerId Owner user ID
     */
    public function __construct(
        public string $id,
        public string $name,
        public ?array $metadata = null,
        public ?string $createdAt = null,
        public ?string $updatedAt = null,
        public ?string $ownerId = null,
    ) {
    }

    /**
     * Create a Dataset from an array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? $data['dataset_id'] ?? '',
            name: $data['name'] ?? '',
            metadata: $data['metadata'] ?? null,
            createdAt: $data['created_at'] ?? $data['createdAt'] ?? null,
            updatedAt: $data['updated_at'] ?? $data['updatedAt'] ?? null,
            ownerId: $data['owner_id'] ?? $data['ownerId'] ?? null,
        );
    }

    /**
     * Convert the Dataset to an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'metadata' => $this->metadata,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
            'owner_id' => $this->ownerId,
        ];
    }
}
