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
        $this->ensureIndexes();
    }

    private function ensureIndexes(): void
    {
        if (self::$indexesEnsured) {
            return;
        }

        $this->collection->createIndex(['job_id' => 1], ['unique' => true]);
        $this->collection->createIndex(['status' => 1]);

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

        $result = $this->collection->insertOne([
            'job_id' => new ObjectId($jobId),
            'amount' => $amount,
            'method' => $method,
            'status' => 'unpaid',
            'created_at' => new UTCDateTime(),
            'updated_at' => new UTCDateTime(),
        ]);

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
}
