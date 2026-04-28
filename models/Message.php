<?php

declare(strict_types=1);

use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Collection;

/*
 |--------------------------------------------------------------------------
 | models/Message.php
 |--------------------------------------------------------------------------
 | Data access object for the 'messages' collection.
 */

class Message
{
    private Collection $collection;
    private static bool $indexesEnsured = false;

    private function isValidObjectId(string $value): bool
    {
        return preg_match('/^[a-f0-9]{24}$/i', $value) === 1;
    }

    public function __construct()
    {
        $this->collection = getMongoDatabase()->selectCollection('messages');
        $this->ensureIndexes();
    }

    private function ensureIndexes(): void
    {
        if (self::$indexesEnsured) {
            return;
        }

        $this->collection->createIndex(['job_id' => 1, 'created_at' => 1]);
        $this->collection->createIndex(['sender_id' => 1]);
        $this->collection->createIndex(['receiver_id' => 1]);

        self::$indexesEnsured = true;
    }

    public function sendMessage(
        string $senderId,
        string $receiverId,
        string $jobId,
        string $messageText
    ): bool {
        if (!$this->isValidObjectId($senderId) || !$this->isValidObjectId($receiverId) || !$this->isValidObjectId($jobId)) {
            throw new InvalidArgumentException('Invalid id provided.');
        }

        if (trim($messageText) === '') {
            throw new InvalidArgumentException('Message cannot be empty.');
        }

        $document = [
            'sender_id' => new ObjectId($senderId),
            'receiver_id' => new ObjectId($receiverId),
            'job_id' => new ObjectId($jobId),
            'message' => trim($messageText),
            'created_at' => new UTCDateTime(),
        ];

        $result = $this->collection->insertOne($document);

        return $result->getInsertedCount() === 1;
    }

    public function getMessagesByJobId(string $jobId): array
    {
        if (!$this->isValidObjectId($jobId)) {
            return [];
        }

        $cursor = $this->collection->find(
            ['job_id' => new ObjectId($jobId)],
            ['sort' => ['created_at' => 1]]
        );

        $messages = [];
        foreach ($cursor as $doc) {
            $messages[] = (array) $doc;
        }

        return $messages;
    }
}
