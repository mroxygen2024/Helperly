<?php

declare(strict_types=1);

use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Collection;

/*
 |--------------------------------------------------------------------------
 | models/Notification.php
 |--------------------------------------------------------------------------
 | Manages user notifications in the marketplace.
 */

class Notification
{
    private Collection $collection;
    private static bool $indexesEnsured = false;

    private function isValidObjectId(string $value): bool
    {
        return preg_match('/^[a-f0-9]{24}$/i', $value) === 1;
    }

    public function __construct()
    {
        $this->collection = getMongoDatabase()->selectCollection('notifications');

    }

    public function ensureIndexes(): void
    {
        if (self::$indexesEnsured) {
            return;
        }

        $this->collection->createIndex(['user_id' => 1, 'created_at' => -1]);
        $this->collection->createIndex(['user_id' => 1, 'is_read' => 1]);

        self::$indexesEnsured = true;
    }

    public function create(
        string $userId,
        string $type,
        string $title,
        string $message,
        ?string $link = null
    ): bool {
        if (!$this->isValidObjectId($userId)) {
            return false;
        }

        $document = [
            'user_id' => new ObjectId($userId),
            'type' => $type, // e.g., 'application', 'message', 'job_accepted', 'verification'
            'title' => $title,
            'message' => $message,
            'link' => $link,
            'is_read' => false,
            'created_at' => new UTCDateTime(),
        ];

        $result = $this->collection->insertOne($document);
        return $result->getInsertedCount() === 1;
    }

    public function getNotificationsByUser(string $userId, int $limit = 20): array
    {
        if (!$this->isValidObjectId($userId)) {
            return [];
        }

        $cursor = $this->collection->find(
            ['user_id' => new ObjectId($userId)],
            [
                'sort' => ['created_at' => -1],
                'limit' => $limit
            ]
        );

        return iterator_to_array($cursor, false);
    }

    public function getUnreadCount(string $userId): int
    {
        if (!$this->isValidObjectId($userId)) {
            return 0;
        }

        return (int) $this->collection->countDocuments([
            'user_id' => new ObjectId($userId),
            'is_read' => false
        ]);
    }

    public function markAsRead(string $notificationId, string $userId): bool
    {
        if (!$this->isValidObjectId($notificationId) || !$this->isValidObjectId($userId)) {
            return false;
        }

        $result = $this->collection->updateOne(
            [
                '_id' => new ObjectId($notificationId),
                'user_id' => new ObjectId($userId)
            ],
            ['$set' => ['is_read' => true]]
        );

        return $result->getModifiedCount() === 1;
    }

    public function markAllAsRead(string $userId): bool
    {
        if (!$this->isValidObjectId($userId)) {
            return false;
        }

        $result = $this->collection->updateMany(
            ['user_id' => new ObjectId($userId), 'is_read' => false],
            ['$set' => ['is_read' => true]]
        );

        return $result->getMatchedCount() > 0;
    }
}
