<?php

declare(strict_types=1);

use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Collection;

/*
 |--------------------------------------------------------------------------
 | models/Payment.php
 |--------------------------------------------------------------------------
 | Tracks payments for completed jobs.
 */

class Payment
{
    private Collection $collection;
    private static bool $indexesEnsured = false;

    private function isValidObjectId(string $value): bool
    {
        return preg_match('/^[a-f0-9]{24}$/i', $value) === 1;
    }

    public function __construct()
    {
        $this->collection = getMongoDatabase()->selectCollection('payments');

    }

    public function ensureIndexes(): void
    {
        if (self::$indexesEnsured) {
            return;
        }

        $this->collection->createIndex(['job_id' => 1], ['unique' => true]);
        $this->collection->createIndex(['status' => 1]);
        $this->collection->createIndex(['parent_id' => 1]);
        $this->collection->createIndex(['provider_id' => 1]);

        self::$indexesEnsured = true;
    }

    public function createPayment(string $jobId, float $amount, string $method = 'cash'): bool
    {
        if (!$this->isValidObjectId($jobId)) {
            return false;
        }

        // Check if already exists to avoid duplicates
        $existing = $this->collection->findOne(['job_id' => new ObjectId($jobId)]);
        if ($existing) {
            return true; 
        }

        // Fetch job to get parent/provider
        $jobModel = new Job();
        $job = $jobModel->getJobById($jobId);
        
        $document = [
            'job_id' => new ObjectId($jobId),
            'amount' => $amount,
            'method' => $method,
            'status' => 'unpaid',
            'created_at' => new UTCDateTime(),
            'updated_at' => new UTCDateTime(),
        ];

        if ($job) {
            if (isset($job['parent_id'])) $document['parent_id'] = $job['parent_id'];
            if (isset($job['selected_provider_id'])) $document['provider_id'] = $job['selected_provider_id'];
        }

        $result = $this->collection->insertOne($document);

        return $result->getInsertedCount() === 1;
    }

    public function getPaymentByJobId(string $jobId): ?array
    {
        if (!$this->isValidObjectId($jobId)) {
            return null;
        }

        $payment = $this->collection->findOne(['job_id' => new ObjectId($jobId)]);
        return $payment ? (array) $payment : null;
    }

    public function updateStatus(string $jobId, string $status): bool
    {
        if (!$this->isValidObjectId($jobId)) {
            return false;
        }

        $result = $this->collection->updateOne(
            ['job_id' => new ObjectId($jobId)],
            [
                '$set' => [
                    'status' => $status,
                    'updated_at' => new UTCDateTime(),
                ]
            ]
        );

        return $result->getModifiedCount() === 1;
    }

    public function getPaymentsByParentId(string $parentId): array
    {
        if (!$this->isValidObjectId($parentId)) {
            return [];
        }

        $cursor = $this->collection->aggregate([
            ['$match' => ['parent_id' => new ObjectId($parentId)]],
            [
                '$lookup' => [
                    'from' => 'jobs',
                    'localField' => 'job_id',
                    'foreignField' => '_id',
                    'as' => 'job'
                ]
            ],
            ['$unwind' => '$job'],
            ['$sort' => ['created_at' => -1]]
        ]);

        return iterator_to_array($cursor, false);
    }

    public function getPaymentsByProviderId(string $providerId): array
    {
        if (!$this->isValidObjectId($providerId)) {
            return [];
        }

        $cursor = $this->collection->aggregate([
            ['$match' => ['provider_id' => new ObjectId($providerId)]],
            [
                '$lookup' => [
                    'from' => 'jobs',
                    'localField' => 'job_id',
                    'foreignField' => '_id',
                    'as' => 'job'
                ]
            ],
            ['$unwind' => '$job'],
            ['$sort' => ['created_at' => -1]]
        ]);

        return iterator_to_array($cursor, false);
    }
}
