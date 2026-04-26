<?php

declare(strict_types=1);

use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Collection;
use MongoDB\Driver\Exception\BulkWriteException;

/*
 |--------------------------------------------------------------------------
 | models/JobApplication.php
 |--------------------------------------------------------------------------
 | Stores job applications from service providers.
 */

class JobApplication
{
    private Collection $collection;
    private static bool $indexesEnsured = false;

    private function isValidObjectId(string $value): bool
    {
        return preg_match('/^[a-f0-9]{24}$/i', $value) === 1;
    }

    public function __construct()
    {
        $this->collection = getMongoDatabase()->selectCollection('job_applications');
        $this->ensureIndexes();
    }

    private function ensureIndexes(): void
    {
        if (self::$indexesEnsured) {
            return;
        }

        $this->collection->createIndex(['job_id' => 1, 'provider_id' => 1], ['unique' => true]);
        $this->collection->createIndex(['provider_id' => 1, 'status' => 1, 'created_at' => -1]);
        $this->collection->createIndex(['job_id' => 1, 'status' => 1]);

        self::$indexesEnsured = true;
    }

    public function createApplication(string $jobId, string $providerId): bool
    {
        if (!$this->isValidObjectId($jobId) || !$this->isValidObjectId($providerId)) {
            throw new InvalidArgumentException('Invalid job id or provider id provided.');
        }

        try {
            $result = $this->collection->insertOne([
                'job_id' => new ObjectId($jobId),
                'provider_id' => new ObjectId($providerId),
                'status' => 'pending',
                'created_at' => new UTCDateTime(),
                'updated_at' => new UTCDateTime(),
            ]);
        } catch (BulkWriteException $exception) {
            if ($exception->getCode() === 11000) {
                throw new RuntimeException('You already applied for this job.');
            }

            throw $exception;
        }

        return $result->getInsertedCount() === 1;
    }

    /**
     * @return array<int, string>
     */
    public function getAppliedJobIdsByProvider(string $providerId): array
    {
        if (!$this->isValidObjectId($providerId)) {
            return [];
        }

        $cursor = $this->collection->find(
            ['provider_id' => new ObjectId($providerId)],
            ['projection' => ['job_id' => 1]]
        );

        $jobIds = [];
        foreach ($cursor as $application) {
            $application = (array) $application;
            if (($application['job_id'] ?? null) instanceof ObjectId) {
                $jobIds[] = (string) $application['job_id'];
            }
        }

        return $jobIds;
    }

    public function getApplicationsForJob(string $jobId): array
    {
        if (!$this->isValidObjectId($jobId)) {
            return [];
        }

        $pipeline = [
            ['$match' => ['job_id' => new ObjectId($jobId)]],
            [
                '$lookup' => [
                    'from' => 'users',
                    'localField' => 'provider_id',
                    'foreignField' => '_id',
                    'as' => 'user_data'
                ]
            ],
            ['$unwind' => [
                'path' => '$user_data',
                'preserveNullAndEmptyArrays' => true
            ]],
            [
                '$lookup' => [
                    'from' => 'servant_profiles',
                    'localField' => 'provider_id',
                    'foreignField' => 'user_id',
                    'as' => 'profile_data'
                ]
            ],
            ['$unwind' => [
                'path' => '$profile_data',
                'preserveNullAndEmptyArrays' => true
            ]],
            ['$sort' => ['created_at' => -1]]
        ];

        $cursor = $this->collection->aggregate($pipeline);
        return iterator_to_array($cursor, false);
    }

    public function updateApplicationStatus(string $jobId, string $providerId, string $status): bool
    {
        if (!$this->isValidObjectId($jobId) || !$this->isValidObjectId($providerId)) {
            return false;
        }

        $result = $this->collection->updateOne(
            [
                'job_id' => new ObjectId($jobId),
                'provider_id' => new ObjectId($providerId)
            ],
            [
                '$set' => [
                    'status' => $status,
                    'updated_at' => new UTCDateTime(),
                ]
            ]
        );

        return $result->getModifiedCount() === 1;
    }

    public function rejectOtherApplicants(string $jobId, string $acceptedProviderId): void
    {
        if (!$this->isValidObjectId($jobId) || !$this->isValidObjectId($acceptedProviderId)) {
            return;
        }

        $this->collection->updateMany(
            [
                'job_id' => new ObjectId($jobId),
                'provider_id' => ['$ne' => new ObjectId($acceptedProviderId)],
                'status' => 'pending'
            ],
            [
                '$set' => [
                    'status' => 'rejected',
                    'updated_at' => new UTCDateTime(),
                ]
            ]
        );
    }

    public function getApplicationsByProvider(string $providerId): array
    {
        if (!$this->isValidObjectId($providerId)) {
            return [];
        }

        $pipeline = [
            ['$match' => ['provider_id' => new \MongoDB\BSON\ObjectId($providerId)]],
            [
                '$lookup' => [
                    'from' => 'jobs',
                    'localField' => 'job_id',
                    'foreignField' => '_id',
                    'as' => 'job'
                ]
            ],
            ['$unwind' => [
                'path' => '$job',
                'preserveNullAndEmptyArrays' => true
            ]],
            ['$sort' => ['created_at' => -1]]
        ];

        $cursor = $this->collection->aggregate($pipeline);
        return iterator_to_array($cursor, false);
    }
}
