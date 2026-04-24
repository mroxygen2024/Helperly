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
    private const VERIFICATION_STATUSES = ['pending', 'approved', 'rejected'];

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
        $this->collection->createIndex(['hourly_rate' => 1]);
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

    private function normalizeVerificationStatus(string $status): string
    {
        $normalized = strtolower(trim($status));
        if (!in_array($normalized, self::VERIFICATION_STATUSES, true)) {
            throw new InvalidArgumentException('Invalid verification status provided.');
        }

        return $normalized;
    }

    private function normalizeVerificationNotes(?string $notes): string
    {
        return trim((string) $notes);
    }

    public function createOrUpdateProfile(
        string $user_id,
        string $fullName,
        string $nationalId,
        int $age,
        string $gender,
        array|string $skills,
        string $experience,
        string $location,
        string $availability,
        string $hourlyRate,
        string $profilePhoto,
        string $faydaIdFrontUrl,
        string $faydaIdBackUrl,
        string $selfieUrl
    ): bool {
        if (!$this->isValidObjectId($user_id)) {
            throw new InvalidArgumentException('Invalid user_id provided.');
        }

        $now = new UTCDateTime();
        $result = $this->collection->updateOne(
            ['user_id' => new ObjectId($user_id)],
            [
                '$set' => [
                    'full_name' => trim($fullName),
                    'national_id' => trim($nationalId),
                    'age' => max(18, min(80, $age)),
                    'gender' => trim($gender),
                    'skills' => $this->normalizeSkills($skills),
                    'experience' => trim($experience),
                    'location' => trim($location),
                    'availability' => trim($availability),
                    'hourly_rate' => trim($hourlyRate),
                    'profile_photo' => trim($profilePhoto),
                    'fayda_id_front_url' => trim($faydaIdFrontUrl),
                    'fayda_id_back_url' => trim($faydaIdBackUrl),
                    'selfie_url' => trim($selfieUrl),
                    'updated_at' => $now,
                ],
                '$setOnInsert' => [
                    'user_id' => new ObjectId($user_id),
                    'verification_status' => 'pending',
                    'verification_notes' => '',
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

    public function isApprovedByUserId(string $user_id): bool
    {
        if (!$this->isValidObjectId($user_id)) {
            return false;
        }

        $profile = $this->collection->findOne(
            [
                'user_id' => new ObjectId($user_id),
                'verification_status' => 'approved',
            ],
            ['projection' => ['_id' => 1]]
        );

        return $profile !== null;
    }

    public function updateVerificationStatus(string $user_id, string $status, ?string $notes = null): bool
    {
        if (!$this->isValidObjectId($user_id)) {
            throw new InvalidArgumentException('Invalid user_id provided.');
        }

        $normalizedStatus = $this->normalizeVerificationStatus($status);
        $normalizedNotes = $this->normalizeVerificationNotes($notes);

        $result = $this->collection->updateOne(
            ['user_id' => new ObjectId($user_id)],
            [
                '$set' => [
                    'verification_status' => $normalizedStatus,
                    'verification_notes' => $normalizedNotes,
                    'updated_at' => new UTCDateTime(),
                ],
            ]
        );

        return $result->getMatchedCount() > 0;
    }

    public static function allowedVerificationStatuses(): array
    {
        return self::VERIFICATION_STATUSES;
    }

    public static function verificationStatusLabel(?string $status): string
    {
        return match (strtolower(trim((string) $status))) {
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            default => 'Pending',
        };
    }

    /**
     * Fetch servant profiles with dynamic filters (regex, ranges, relationships).
     *
     * @return array<int, array<string, mixed>>
     */
    public function findProfilesByFilters(array $filters = [], int $limit = 50): array
    {
        $limit = max(1, min($limit, 100));
        
        $initialMatch = ['verification_status' => 'approved'];

        if (!empty($filters['location'])) {
            $initialMatch['location'] = new \MongoDB\BSON\Regex(preg_quote(trim($filters['location'])), 'i');
        }
        if (!empty($filters['skill'])) {
            $initialMatch['skills'] = new \MongoDB\BSON\Regex(preg_quote(trim($filters['skill'])), 'i');
        }
        if (!empty($filters['experience'])) {
            $initialMatch['experience'] = new \MongoDB\BSON\Regex(preg_quote(trim($filters['experience'])), 'i');
        }
        if (!empty($filters['availability'])) {
            $initialMatch['availability'] = new \MongoDB\BSON\Regex(preg_quote(trim($filters['availability'])), 'i');
        }
        if (!empty($filters['name'])) {
            $initialMatch['full_name'] = new \MongoDB\BSON\Regex(preg_quote(trim($filters['name'])), 'i');
        }
        if (isset($filters['rating']) && $filters['rating'] !== '') {
            // Note: rating may need to be mapped to review collection eventually
            $initialMatch['rating'] = ['$gte' => (float)$filters['rating']];
        }

        $pipeline = [
            ['$match' => $initialMatch]
        ];

        if (!empty($filters['service_type'])) {
            $pipeline[] = [
                '$lookup' => [
                    'from' => 'services',
                    'localField' => 'user_id',
                    'foreignField' => 'provider_id',
                    'as' => 'services_info'
                ]
            ];
            $pipeline[] = [
                '$match' => [
                    'services_info.service_type' => new \MongoDB\BSON\Regex(preg_quote(trim($filters['service_type'])), 'i')
                ]
            ];
        }
        
        if ((isset($filters['min_price']) && $filters['min_price'] !== '') || (isset($filters['max_price']) && $filters['max_price'] !== '')) {
            $pipeline[] = [
                '$addFields' => [
                    'numeric_rate' => [
                        '$convert' => [
                            'input' => '$hourly_rate',
                            'to' => 'double',
                            'onError' => 0.0,
                            'onNull' => 0.0
                        ]
                    ]
                ]
            ];
            $priceFilter = [];
            if (isset($filters['min_price']) && $filters['min_price'] !== '') {
                $priceFilter['$gte'] = (float)$filters['min_price'];
            }
            if (isset($filters['max_price']) && $filters['max_price'] !== '') {
                $priceFilter['$lte'] = (float)$filters['max_price'];
            }
            $pipeline[] = [
                '$match' => [
                    'numeric_rate' => $priceFilter
                ]
            ];
        }

        $pipeline[] = ['$limit' => $limit];

        $pipeline[] = ['$project' => [
            'user_id' => 1,
            'full_name' => 1,
            'gender' => 1,
            'skills' => 1,
            'experience' => 1,
            'location' => 1,
            'availability' => 1,
            'hourly_rate' => 1,
            'profile_photo' => 1,
            'fayda_id_front_url' => 1,
            'fayda_id_back_url' => 1,
            'selfie_url' => 1,
            'verification_status' => 1,
            'verification_notes' => 1,
            'created_at' => 1,
        ]];
        
        $cursor = $this->collection->aggregate($pipeline);
        return iterator_to_array($cursor, false);
    }

    /**
     * Fetch only profiles waiting for admin verification.
     *
     * @return array<int, array<string, mixed>>
     */
    public function findPendingProfiles(int $limit = 100): array
    {
        $limit = max(1, min($limit, 200));

        $cursor = $this->collection->find(
            [
                '$or' => [
                    ['verification_status' => 'pending'],
                    ['verification_status' => ['$exists' => false]],
                ],
            ],
            [
                'limit' => $limit,
                'sort' => ['updated_at' => -1, 'created_at' => -1],
                'projection' => [
                    'user_id' => 1,
                    'full_name' => 1,
                    'location' => 1,
                    'fayda_id_front_url' => 1,
                    'fayda_id_back_url' => 1,
                    'selfie_url' => 1,
                    'verification_status' => 1,
                    'verification_notes' => 1,
                    'created_at' => 1,
                    'updated_at' => 1,
                ],
            ]
        );

        return iterator_to_array($cursor, false);
    }
}
