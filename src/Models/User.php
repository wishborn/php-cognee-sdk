<?php

declare(strict_types=1);

namespace Cognee\Models;

/**
 * User model.
 */
readonly class User
{
    /**
     * @param string $id User ID
     * @param string $email User email
     * @param string|null $name User name
     * @param string|null $createdAt Creation timestamp
     */
    public function __construct(
        public string $id,
        public string $email,
        public ?string $name = null,
        public ?string $createdAt = null,
    ) {
    }

    /**
     * Create a User from an array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? $data['user_id'] ?? '',
            email: $data['email'] ?? '',
            name: $data['name'] ?? null,
            createdAt: $data['created_at'] ?? $data['createdAt'] ?? null,
        );
    }

    /**
     * Convert the User to an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'name' => $this->name,
            'created_at' => $this->createdAt,
        ];
    }
}
