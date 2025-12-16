<?php

declare(strict_types=1);

namespace Cognee\Tests\Unit\Models;

use Cognee\Models\Dataset;
use PHPUnit\Framework\TestCase;

class DatasetTest extends TestCase
{
    public function testFromArrayCreatesDataset(): void
    {
        $data = [
            'id' => '123',
            'name' => 'Test Dataset',
            'metadata' => ['key' => 'value'],
            'created_at' => '2024-01-01T00:00:00Z',
            'updated_at' => '2024-01-02T00:00:00Z',
            'owner_id' => 'user-1',
        ];

        $dataset = Dataset::fromArray($data);

        $this->assertSame('123', $dataset->id);
        $this->assertSame('Test Dataset', $dataset->name);
        $this->assertSame(['key' => 'value'], $dataset->metadata);
        $this->assertSame('2024-01-01T00:00:00Z', $dataset->createdAt);
        $this->assertSame('2024-01-02T00:00:00Z', $dataset->updatedAt);
        $this->assertSame('user-1', $dataset->ownerId);
    }

    public function testToArrayConvertsDatasetToArray(): void
    {
        $dataset = new Dataset(
            id: '123',
            name: 'Test Dataset',
            metadata: ['key' => 'value'],
            createdAt: '2024-01-01T00:00:00Z',
            updatedAt: '2024-01-02T00:00:00Z',
            ownerId: 'user-1',
        );

        $array = $dataset->toArray();

        $this->assertSame('123', $array['id']);
        $this->assertSame('Test Dataset', $array['name']);
        $this->assertSame(['key' => 'value'], $array['metadata']);
    }
}
