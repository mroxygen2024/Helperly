<?php

declare(strict_types=1);

use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Collection;
use MongoDB\Driver\Exception\BulkWriteException;

/*
 |--------------------------------------------------------------------------
 | models/ purpose
 |--------------------------------------------------------------------------
 | Data access objects for MongoDB collections and related domain behavior.
 */

class HireRequest
{
    private Collection $collection;
    private static bool $indexesEnsured = false;

    private function isValidObjectId(string $value): bool
    {
        return preg_match('/^[a-f0-9]{24}$/i', $value) === 1;
    }

    public function __construct()
    {
        $this->collection = getMongoDatabase()->selectCollection('hire_requests');
        $this->ensureIndexes();
    }

    private function ensureIndexes(): void
    {
        if (self::$indexesEnsured) {
            return;
        }

        // Allow only one pending request per employer-servant pair.
        $this->collection->createIndex(
            ['employer_id' => 1, 'servant_id' => 1],
            [
                'unique' => true,
                'partialFilterExpression' => ['status' => 'pending'],
            ]
        );

        $this->collection->createIndex(['servant_id' => 1, 'status' => 1, 'created_at' => -1]);
        self::$indexesEnsured = true;
    }

    public function createRequest(string $employer_id, string $servant_id): bool
    {
        if (!$this->isValidObjectId($employer_id) || !$this->isValidObjectId($servant_id)) {
            throw new InvalidArgumentException('Invalid employer_id or servant_id provided.');
        }

        if ($employer_id === $servant_id) {
            throw new RuntimeException('Invalid request target.');
        }

        $existing = $this->collection->findOne([
            'employer_id' => new ObjectId($employer_id),
            'servant_id' => new ObjectId($servant_id),
            'status' => 'pending',
        ]);

        if ($existing) {
            throw new RuntimeException('You already have a pending request for this servant.');
        }

        try {
            $result = $this->collection->insertOne([
                'employer_id' => new ObjectId($employer_id),
                'servant_id' => new ObjectId($servant_id),
                'status' => 'pending',
                'created_at' => new UTCDateTime(),
            ]);
        } catch (BulkWriteException $exception) {
            if ($exception->getCode() === 11000) {
                throw new RuntimeException('You already have a pending request for this servant.');
            }
            throw $exception;
        }

        return $result->getInsertedCount() === 1;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getRequestsForServant(string $servant_id): array
    {
        if (!$this->isValidObjectId($servant_id)) {
            return [];
        }

        $cursor = $this->collection->find(
            ['servant_id' => new ObjectId($servant_id)],
            [
                'sort' => ['created_at' => -1],
            ]
        );

        return iterator_to_array($cursor, false);
    }

    public function updateRequestStatus(string $request_id, string $status): bool
    {
        if (!$this->isValidObjectId($request_id)) {
            return false;
        }

        $normalizedStatus = trim($status);
        if (!in_array($normalizedStatus, ['pending', 'accepted', 'rejected'], true)) {
            throw new InvalidArgumentException('Invalid status value.');
        }

        $result = $this->collection->updateOne(
            [
                '_id' => new ObjectId($request_id),
                'status' => 'pending',
            ],
            [
                '$set' => ['status' => $normalizedStatus],
            ]
        );

        return $result->getModifiedCount() > 0;
    }

    public function getRequestById(string $request_id): ?array
    {
        if (!$this->isValidObjectId($request_id)) {
            return null;
        }

        $request = $this->collection->findOne(['_id' => new ObjectId($request_id)]);
        return $request ? (array) $request : null;
    }
}
