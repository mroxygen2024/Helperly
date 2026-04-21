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

class EmployerProfile
{
    private Collection $collection;
    private static bool $indexesEnsured = false;

    private function isValidObjectId(string $value): bool
    {
        return preg_match('/^[a-f0-9]{24}$/i', $value) === 1;
    }

    public function __construct()
    {
        $this->collection = getMongoDatabase()->selectCollection('employer_profiles');
        $this->ensureIndexes();
    }

    private function ensureIndexes(): void
    {
        if (self::$indexesEnsured) {
            return;
        }

        // Enforce one employer profile per user.
        $this->collection->createIndex(['user_id' => 1], ['unique' => true]);
        self::$indexesEnsured = true;
    }

    public function createOrUpdateProfile(string $user_id, string $address, string $location): bool
    {
        if (!$this->isValidObjectId($user_id)) {
            throw new InvalidArgumentException('Invalid user_id provided.');
        }

        $now = new UTCDateTime();
        $result = $this->collection->updateOne(
            ['user_id' => new ObjectId($user_id)],
            [
                '$set' => [
                    'address' => trim($address),
                    'location' => trim($location),
                    'updated_at' => $now,
                ],
                '$setOnInsert' => [
                    'user_id' => new ObjectId($user_id),
                    'created_at' => $now,
                ],
            ],
            ['upsert' => true]
        );

        return $result->getUpsertedCount() > 0 || $result->getModifiedCount() > 0 || $result->getMatchedCount() > 0;
    }

    public function getProfileByUserId(string $user_id): ?array
    {
        if (!$this->isValidObjectId($user_id)) {
            return null;
        }

        $profile = $this->collection->findOne(['user_id' => new ObjectId($user_id)]);
        return $profile ? (array) $profile : null;
    }
}
