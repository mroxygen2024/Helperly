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

class ServantProfile
{
    private Collection $collection;
    private static bool $indexesEnsured = false;

    private function isValidObjectId(string $value): bool
    {
        return preg_match('/^[a-f0-9]{24}$/i', $value) === 1;
    }

    public function __construct()
    {
        $this->collection = getMongoDatabase()->selectCollection('servant_profiles');
        $this->ensureIndexes();
    }

    private function ensureIndexes(): void
    {
        if (self::$indexesEnsured) {
            return;
        }

        // One profile per user is enforced with a unique index.
        $this->collection->createIndex(['user_id' => 1], ['unique' => true]);
        // Support the servant directory filters without scanning the whole collection.
        $this->collection->createIndex(['location' => 1, 'skills' => 1]);
        $this->collection->createIndex(['skills' => 1]);
        self::$indexesEnsured = true;
    }

    private function normalizeSkills(array|string $skills): array
    {
        // Accept either CSV string or array and persist a clean, unique string array.
        if (is_string($skills)) {
            $skills = explode(',', $skills);
        }

        $normalized = [];
        foreach ($skills as $skill) {
            $value = trim((string) $skill);
            if ($value !== '') {
                $normalized[] = $value;
            }
        }

        return array_values(array_unique($normalized));
    }

    public function createOrUpdateProfile(
        string $user_id,
        array|string $skills,
        string $experience,
        string $location,
        string $availability
    ): bool {
        if (!$this->isValidObjectId($user_id)) {
            throw new InvalidArgumentException('Invalid user_id provided.');
        }

        $now = new UTCDateTime();
        $result = $this->collection->updateOne(
            ['user_id' => new ObjectId($user_id)],
            [
                '$set' => [
                    'skills' => $this->normalizeSkills($skills),
                    'experience' => trim($experience),
                    'location' => trim($location),
                    'availability' => trim($availability),
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

    /**
     * Fetch servant profiles with optional exact-match filters.
     *
     * @return array<int, array<string, mixed>>
     */
    public function findProfilesByFilters(string $location = '', string $skill = '', int $limit = 50): array
    {
        $filter = [];

        $location = trim($location);
        $skill = trim($skill);

        if ($location !== '') {
            $filter['location'] = $location;
        }

        if ($skill !== '') {
            $filter['skills'] = $skill;
        }

        $limit = max(1, min($limit, 100));

        $cursor = $this->collection->find(
            $filter,
            [
                'limit' => $limit,
                'projection' => [
                    'user_id' => 1,
                    'skills' => 1,
                    'experience' => 1,
                    'location' => 1,
                    'availability' => 1,
                    'created_at' => 1,
                ],
            ]
        );

        return iterator_to_array($cursor, false);
    }
}
