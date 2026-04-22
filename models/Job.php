<?php

declare(strict_types=1);

use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Collection;

/*
 |--------------------------------------------------------------------------
 | models/ purpose
 |--------------------------------------------------------------------------
 | Data access objects for MongoDB collections and related domain behavior.
 */

class Job
{
    private Collection $collection;
    private static bool $indexesEnsured = false;

    private function isValidObjectId(string $value): bool
    {
        return preg_match('/^[a-f0-9]{24}$/i', $value) === 1;
    }

    public function __construct()
    {
        $this->collection = getMongoDatabase()->selectCollection('jobs');
        $this->ensureIndexes();
    }

    private function ensureIndexes(): void
    {
        if (self::$indexesEnsured) {
            return;
        }

        $this->collection->createIndex(['parent_id' => 1, 'created_at' => -1]);
        $this->collection->createIndex(['status' => 1, 'time' => 1]);
        $this->collection->createIndex(['service_type' => 1, 'location' => 1]);

        self::$indexesEnsured = true;
    }

    public function createJob(
        string $parentId,
        string $time,
        string $duration,
        string $serviceType,
        string $location,
        string $instructions
    ): bool {
        if (!$this->isValidObjectId($parentId)) {
            throw new InvalidArgumentException('Invalid parent id provided.');
        }

        $parsedTime = strtotime($time);
        if ($parsedTime === false) {
            throw new InvalidArgumentException('Invalid time value provided.');
        }

        $document = [
            'parent_id' => new ObjectId($parentId),
            'time' => new UTCDateTime($parsedTime * 1000),
            'duration' => $duration,
            'service_type' => $serviceType,
            'location' => $location,
            'instructions' => $instructions,
            'status' => 'open',
            'created_at' => new UTCDateTime(),
            'updated_at' => new UTCDateTime(),
        ];

        $result = $this->collection->insertOne($document);

        return $result->getInsertedCount() === 1;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getOpenJobs(): array
    {
        $cursor = $this->collection->find(
            ['status' => 'open'],
            ['sort' => ['created_at' => -1]]
        );

        return iterator_to_array($cursor, false);
    }

    public function getJobById(string $jobId): ?array
    {
        if (!$this->isValidObjectId($jobId)) {
            return null;
        }

        $job = $this->collection->findOne(['_id' => new ObjectId($jobId)]);
        return $job ? (array) $job : null;
    }
}
