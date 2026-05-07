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

    }

    public function ensureIndexes(): void
    {
        if (self::$indexesEnsured) {
            return;
        }

        // Enforce one employer profile per user.
        $this->collection->createIndex(['user_id' => 1], ['unique' => true]);
        self::$indexesEnsured = true;
    }

    private function normalizeStringArray(array|string $values): array
    {
        if (is_string($values)) {
            $values = explode(',', $values);
        }

        $normalized = [];
        foreach ($values as $value) {
            $item = trim((string) $value);
            if ($item !== '') {
                $normalized[] = $item;
            }
        }

        return array_values(array_unique($normalized));
    }

    private function normalizeIntegerArray(array|string $values): array
    {
        if (is_string($values)) {
            $values = explode(',', $values);
        }

        $normalized = [];
        foreach ($values as $value) {
            $number = (int) trim((string) $value);
            if ($number > 0 && $number <= 25) {
                $normalized[] = $number;
            }
        }

        $normalized = array_values(array_unique($normalized));
        sort($normalized);

        return $normalized;
    }

    public function createOrUpdateProfile(
        string $user_id,
        string $address,
        string $location,
        array|string $emergencyContacts,
        array|string $childrenAges,
        array|string $preferences
    ): bool
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
                    'emergency_contacts' => $this->normalizeStringArray($emergencyContacts),
                    'children_ages' => $this->normalizeIntegerArray($childrenAges),
                    'preferences' => $this->normalizeStringArray($preferences),
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

        $profile = $this->collection->findOne(
            ['user_id' => new ObjectId($user_id)],
            [
                'projection' => [
                    'user_id' => 1,
                    'address' => 1,
                    'location' => 1,
                    'emergency_contacts' => 1,
                    'children_ages' => 1,
                    'preferences' => 1,
                    'created_at' => 1,
                    'updated_at' => 1,
                ],
            ]
        );

        return $profile ? (array) $profile : null;
    }
}
