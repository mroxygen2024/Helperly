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
        string $instructions,
        ?string $selectedProviderId = null,
        float $hourlyRate = 0.0,
        float $totalCost = 0.0
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
            'hourly_rate' => $hourlyRate,
            'total_cost' => $totalCost,
            'service_type' => $serviceType,
            'location' => $location,
            'instructions' => $instructions,
            'status' => $selectedProviderId ? 'active' : 'open',
            'created_at' => new UTCDateTime(),
            'updated_at' => new UTCDateTime(),
        ];

        if ($selectedProviderId) {
            $document['selected_provider_id'] = new ObjectId($selectedProviderId);
            $document['parent_confirmed'] = false;
            $document['provider_confirmed'] = false;
        }

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

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getJobsByParentId(string $parentId): array
    {
        if (!$this->isValidObjectId($parentId)) {
            return [];
        }

        $cursor = $this->collection->find(
            ['parent_id' => new ObjectId($parentId)],
            ['sort' => ['created_at' => -1]]
        );

        return iterator_to_array($cursor, false);
    }

    public function updateStatus(string $jobId, string $status): bool
    {
        if (!$this->isValidObjectId($jobId)) {
            return false;
        }

        $result = $this->collection->updateOne(
            ['_id' => new ObjectId($jobId)],
            [
                '$set' => [
                    'status' => $status,
                    'updated_at' => new UTCDateTime(),
                ]
            ]
        );

        return $result->getModifiedCount() === 1;
    }

    public function acceptProvider(string $jobId, string $providerId, float $hourlyRate = 0.0, float $totalCost = 0.0): bool
    {
        if (!$this->isValidObjectId($jobId) || !$this->isValidObjectId($providerId)) {
            return false;
        }

        $result = $this->collection->updateOne(
            ['_id' => new ObjectId($jobId)],
            [
                '$set' => [
                    'selected_provider_id' => new ObjectId($providerId),
                    'status' => 'active',
                    'parent_confirmed' => false,
                    'provider_confirmed' => false,
                    'hourly_rate' => $hourlyRate,
                    'total_cost' => $totalCost,
                    'updated_at' => new UTCDateTime(),
                ]
            ]
        );

        return $result->getModifiedCount() === 1;
    }

    public function confirmJob(string $jobId, string $userId, string $role): bool
    {
        if (!$this->isValidObjectId($jobId) || !$this->isValidObjectId($userId)) {
            return false;
        }

        $query = [
            '_id' => new ObjectId($jobId),
            'status' => 'active'
        ];

        if ($role === 'parent') {
            $query['parent_id'] = new ObjectId($userId);
            $field = 'parent_confirmed';
        } else {
            $query['selected_provider_id'] = new ObjectId($userId);
            $field = 'provider_confirmed';
        }

        $result = $this->collection->updateOne(
            $query,
            ['$set' => [$field => true, 'updated_at' => new UTCDateTime()]]
        );

        if ($result->getMatchedCount() === 0) {
            return false;
        }

        // Check if both confirmed
        $job = $this->collection->findOne(['_id' => new ObjectId($jobId)]);
        if ($job && ($job['parent_confirmed'] ?? false) && ($job['provider_confirmed'] ?? false)) {
            $this->updateStatus($jobId, 'completed');

            // Trigger payment creation
            if (class_exists('Payment')) {
                $payment = new Payment();
                $payment->createPayment($jobId, (float) ($job['total_cost'] ?? 0));
            }
        }

        return true;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getActiveJobsByProvider(string $providerId): array
    {
        if (!$this->isValidObjectId($providerId)) {
            return [];
        }

        $cursor = $this->collection->find(
            [
                'selected_provider_id' => new ObjectId($providerId),
                'status' => 'active'
            ],
            ['sort' => ['created_at' => -1]]
        );

        return iterator_to_array($cursor, false);
    }

    public function stopJob(string $jobId, string $parentId): bool
    {
        if (!$this->isValidObjectId($jobId) || !$this->isValidObjectId($parentId)) {
            return false;
        }

        $result = $this->collection->updateOne(
            [
                '_id' => new ObjectId($jobId),
                'parent_id' => new ObjectId($parentId),
                'status' => ['$in' => ['open', 'active']],
            ],
            [
                '$set' => [
                    'status' => 'cancelled',
                    'updated_at' => new UTCDateTime(),
                ]
            ]
        );

        return $result->getModifiedCount() === 1;
    }

    public function getAllJobs(int $limit = 50): array
    {
        $cursor = $this->collection->find(
            [],
            ['sort' => ['created_at' => -1], 'limit' => $limit]
        );

        return iterator_to_array($cursor, false);
    }

    public function countJobs(): int
    {
        return $this->collection->countDocuments();
    }
}
