<?php

declare(strict_types=1);

use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Collection;
use MongoDB\Driver\Exception\BulkWriteException;

/*
 |--------------------------------------------------------------------------
 | models/ purpose
 |--------------------------------------------------------------------------
 | Data access objects for MongoDB collections and related domain behavior.
 */

class User
{
    private Collection $collection;
    private static bool $indexesEnsured = false;

    private function isValidObjectId(string $value): bool
    {
        return preg_match('/^[a-f0-9]{24}$/i', $value) === 1;
    }

    public function __construct()
    {
        $this->collection = getMongoDatabase()->selectCollection('users');
        $this->ensureIndexes();
    }

    private function ensureIndexes(): void
    {
        if (self::$indexesEnsured) {
            return;
        }

        // Unique email index prevents duplicates at the database level.
        $this->collection->createIndex(['email' => 1], ['unique' => true]);
        self::$indexesEnsured = true;
    }

    private function normalizeEmail(string $email): string
    {
        return strtolower(trim($email));
    }

    private function dateToUtcDateTime(DateTimeImmutable $date): UTCDateTime
    {
        return new UTCDateTime($date->getTimestamp() * 1000);
    }

    public function createUser(
        string $name,
        string $email,
        string $phone,
        string $password,
        string $role,
        string $verificationToken,
        bool $isVerified = false
    ): string
    {
        $normalizedEmail = $this->normalizeEmail($email);
        $normalizedRole = normalizeRole($role);

        // Fast pre-check to provide a clear validation message.
        if ($this->findUserByEmail($normalizedEmail) !== null) {
            throw new RuntimeException('Email is already registered.');
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        if ($passwordHash === false) {
            throw new RuntimeException('Failed to hash password.');
        }

        $document = [
            'name' => trim($name),
            'email' => $normalizedEmail,
            'phone' => trim($phone),
            'password_hash' => $passwordHash,
            'role' => $normalizedRole,
            'is_verified' => $isVerified,
            'created_at' => new UTCDateTime(),
        ];

        if (!$isVerified) {
            $document['verification_token'] = hashVerificationToken($verificationToken);
            $document['verification_sent_at'] = new UTCDateTime();
        }

        try {
            $result = $this->collection->insertOne($document);
        } catch (BulkWriteException $exception) {
            // Handles race conditions where a duplicate email is inserted concurrently.
            if ($exception->getCode() === 11000) {
                throw new RuntimeException('Email is already registered.');
            }
            throw $exception;
        }

        return (string) $result->getInsertedId();
    }

    public function findUserByEmail(string $email): ?array
    {
        $user = $this->collection->findOne(['email' => $this->normalizeEmail($email)]);
        return $user ? (array) $user : null;
    }

    public function verifyUserByToken(string $token): bool
    {
        $tokenHash = hashVerificationToken($token);

        $result = $this->collection->updateOne(
            [
                'verification_token' => $tokenHash,
                'is_verified' => false,
            ],
            [
                '$set' => [
                    'is_verified' => true,
                    'verified_at' => new UTCDateTime(),
                ],
                '$unset' => [
                    'verification_token' => '',
                    'verification_sent_at' => '',
                ],
            ]
        );

        return $result->getModifiedCount() > 0;
    }

    public function createPasswordResetToken(string $email, string $token, int $ttlSeconds = 3600): bool
    {
        $normalizedEmail = $this->normalizeEmail($email);
        $expiresAt = new DateTimeImmutable('+' . max(60, $ttlSeconds) . ' seconds');

        $result = $this->collection->updateOne(
            ['email' => $normalizedEmail],
            [
                '$set' => [
                    'password_reset_token' => hashVerificationToken($token),
                    'password_reset_expires_at' => $this->dateToUtcDateTime($expiresAt),
                    'password_reset_requested_at' => new UTCDateTime(),
                ],
            ]
        );

        return $result->getMatchedCount() > 0;
    }

    public function resetPasswordByToken(string $token, string $newPassword): bool
    {
        $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
        if ($passwordHash === false) {
            throw new RuntimeException('Failed to hash password.');
        }

        $now = new UTCDateTime();
        $tokenHash = hashVerificationToken($token);

        $result = $this->collection->updateOne(
            [
                'password_reset_token' => $tokenHash,
                'password_reset_expires_at' => ['$gt' => $now],
            ],
            [
                '$set' => [
                    'password_hash' => $passwordHash,
                    'password_updated_at' => $now,
                ],
                '$unset' => [
                    'password_reset_token' => '',
                    'password_reset_expires_at' => '',
                    'password_reset_requested_at' => '',
                ],
            ]
        );

        return $result->getModifiedCount() > 0;
    }

    public function findUserById(string $id): ?array
    {
        if (!$this->isValidObjectId($id)) {
            return null;
        }

        $user = $this->collection->findOne(['_id' => new ObjectId($id)]);
        return $user ? (array) $user : null;
    }

    /**
     * Fetch multiple users in one query so controllers can attach user data without N+1 lookups.
     *
     * @param array<int, string> $ids
     * @return array<string, array<string, mixed>>
     */
    public function findUsersByIds(array $ids): array
    {
        $objectIds = [];
        foreach ($ids as $id) {
            $value = (string) $id;
            if ($this->isValidObjectId($value)) {
                $objectIds[] = new ObjectId($value);
            }
        }

        if (empty($objectIds)) {
            return [];
        }

        $cursor = $this->collection->find(
            ['_id' => ['$in' => $objectIds]],
            [
                'projection' => [
                    'name' => 1,
                    'phone' => 1,
                ],
            ]
        );

        $users = [];
        foreach ($cursor as $user) {
            $userArray = (array) $user;
            $id = (string) ($userArray['_id'] ?? '');
            if ($id !== '') {
                $users[$id] = $userArray;
            }
        }

        return $users;
    }

    public function updateBasicProfile(string $id, string $name, string $phone): bool
    {
        if (!$this->isValidObjectId($id)) {
            throw new InvalidArgumentException('Invalid user id provided.');
        }

        $result = $this->collection->updateOne(
            ['_id' => new ObjectId($id)],
            [
                '$set' => [
                    'name' => trim($name),
                    'phone' => trim($phone),
                    'updated_at' => new UTCDateTime(),
                ],
            ]
        );

        return $result->getMatchedCount() > 0;
    }

    // Backward-compatible aliases for existing controller usage.
    public function findByEmail(string $email): ?array
    {
        return $this->findUserByEmail($email);
    }

    public function findById(string $id): ?array
    {
        return $this->findUserById($id);
    }

    public function getAllUsers(int $limit = 100): array
    {
        $cursor = $this->collection->find([], [
            'limit' => $limit,
            'sort' => ['created_at' => -1]
        ]);
        return iterator_to_array($cursor, false);
    }

    public function updateBlockedStatus(string $id, bool $isBlocked): bool
    {
        if (!$this->isValidObjectId($id)) {
            return false;
        }

        $result = $this->collection->updateOne(
            ['_id' => new ObjectId($id)],
            [
                '$set' => [
                    'is_blocked' => $isBlocked,
                    'updated_at' => new UTCDateTime()
                ]
            ]
        );

        return $result->getMatchedCount() > 0;
    }

    public function deleteUser(string $id): bool
    {
        if (!$this->isValidObjectId($id)) {
            return false;
        }

        $result = $this->collection->deleteOne(['_id' => new ObjectId($id)]);
        return $result->getDeletedCount() === 1;
    }

    public function countUsers(): int
    {
        return $this->collection->countDocuments();
    }
}
