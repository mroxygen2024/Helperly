<?php

declare(strict_types=1);

use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Collection;

/*
 |--------------------------------------------------------------------------
 | models/Service.php
 |--------------------------------------------------------------------------
 | Data access object for the 'services' collection.
 */

class Service
{
    private Collection $collection;
    private static bool $indexesEnsured = false;

    private function isValidObjectId(string $value): bool
    {
        return preg_match('/^[a-f0-9]{24}$/i', $value) === 1;
    }

    public function __construct()
    {
        $this->collection = getMongoDatabase()->selectCollection('services');

    }

    public function ensureIndexes(): void
    {
        if (self::$indexesEnsured) {
            return;
        }

        $this->collection->createIndex(['provider_id' => 1]);
        $this->collection->createIndex(['service_type' => 1]);

        self::$indexesEnsured = true;
    }

    public function createService(
        string $providerId,
        string $serviceType,
        string $description,
        string $price,
        string $availability
    ): bool {
        if (!$this->isValidObjectId($providerId)) {
            throw new InvalidArgumentException('Invalid provider id provided.');
        }

        $document = [
            'provider_id' => new ObjectId($providerId),
            'service_type' => trim($serviceType),
            'description' => trim($description),
            'price' => trim($price),
            'availability' => trim($availability),
            'created_at' => new UTCDateTime(),
            'updated_at' => new UTCDateTime(),
        ];

        $result = $this->collection->insertOne($document);

        return $result->getInsertedCount() === 1;
    }

    public function getServicesByProvider(string $providerId): array
    {
        if (!$this->isValidObjectId($providerId)) {
            return [];
        }

        $cursor = $this->collection->find(
            ['provider_id' => new ObjectId($providerId)],
            ['sort' => ['created_at' => -1]]
        );

        return iterator_to_array($cursor, false);
    }
}
