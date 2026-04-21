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

    public function createUser(
        string $name,
        string $email,
        string $phone,
        string $password,
        string $role,
        string $verificationToken
    ): string
    {
        $normalizedEmail = $this->normalizeEmail($email);

        // Fast pre-check to provide a clear validation message.
        if ($this->findUserByEmail($normalizedEmail) !== null) {
            throw new RuntimeException('Email is already registered.');
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        if ($passwordHash === false) {
            throw new RuntimeException('Failed to hash password.');
        }

        try {
            $result = $this->collection->insertOne([
                'name' => trim($name),
                'email' => $normalizedEmail,
                'phone' => trim($phone),
                'password_hash' => $passwordHash,
                'role' => trim($role),
                'is_verified' => false,
                'verification_token' => hashVerificationToken($verificationToken),
                'verification_sent_at' => new UTCDateTime(),
                'created_at' => new UTCDateTime(),
            ]);
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

    // Backward-compatible aliases for existing controller usage.
    public function findByEmail(string $email): ?array
    {
        return $this->findUserByEmail($email);
    }

    public function findById(string $id): ?array
    {
        return $this->findUserById($id);
    }
}
