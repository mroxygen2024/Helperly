<?php

declare(strict_types=1);

use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Collection;

/*
 |--------------------------------------------------------------------------
 | models/Review.php
 |--------------------------------------------------------------------------
 | Stores ratings and reviews for service providers.
 */

class Review
{
    private Collection $collection;
    private static bool $indexesEnsured = false;

    private function isValidObjectId(string $value): bool
    {
        return preg_match('/^[a-f0-9]{24}$/i', $value) === 1;
    }

    public function __construct()
    {
        $this->collection = getMongoDatabase()->selectCollection('reviews');
        $this->ensureIndexes();
    }

    private function ensureIndexes(): void
    {
        if (self::$indexesEnsured) {
            return;
        }

        $this->collection->createIndex(['job_id' => 1], ['unique' => true]);
        $this->collection->createIndex(['provider_id' => 1]);
        $this->collection->createIndex(['parent_id' => 1]);

        self::$indexesEnsured = true;
    }

    public function createReview(
        string $jobId,
        string $providerId,
        string $parentId,
        int $rating,
        string $reviewText
    ): bool {
        if (!$this->isValidObjectId($jobId) || !$this->isValidObjectId($providerId) || !$this->isValidObjectId($parentId)) {
            return false;
        }

        if ($rating < 1 || $rating > 5) {
            throw new InvalidArgumentException('Rating must be between 1 and 5.');
        }

        $existing = $this->collection->findOne(['job_id' => new ObjectId($jobId)]);
        if ($existing) {
            return false;
        }

        $result = $this->collection->insertOne([
            'job_id' => new ObjectId($jobId),
            'provider_id' => new ObjectId($providerId),
            'parent_id' => new ObjectId($parentId),
            'rating' => $rating,
            'review_text' => trim($reviewText),
            'created_at' => new UTCDateTime(),
        ]);

        return $result->getInsertedCount() === 1;
    }

    public function getReviewByJobId(string $jobId): ?array
    {
        if (!$this->isValidObjectId($jobId)) {
            return null;
        }

        $review = $this->collection->findOne(['job_id' => new ObjectId($jobId)]);
        return $review ? (array) $review : null;
    }

    public function getReviewsForProvider(string $providerId): array
    {
        if (!$this->isValidObjectId($providerId)) {
            return [];
        }

        $cursor = $this->collection->find(['provider_id' => new ObjectId($providerId)], ['sort' => ['created_at' => -1]]);
        return iterator_to_array($cursor, false);
    }

    public function calculateAverageRating(string $providerId): array
    {
        if (!$this->isValidObjectId($providerId)) {
            return ['average' => 0, 'count' => 0];
        }

        $pipeline = [
            ['$match' => ['provider_id' => new ObjectId($providerId)]],
            [
                '$group' => [
                    '_id' => '$provider_id',
                    'average' => ['$avg' => '$rating'],
                    'count' => ['$sum' => 1]
                ]
            ]
        ];

        $cursor = $this->collection->aggregate($pipeline);
        $result = iterator_to_array($cursor, false);

        if (empty($result)) {
            return ['average' => 0, 'count' => 0];
        }

        return [
            'average' => (float) $result[0]['average'],
            'count' => (int) $result[0]['count']
        ];
    }
}
