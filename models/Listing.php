<?php

declare(strict_types=1);

use MongoDB\BSON\UTCDateTime;
use MongoDB\Collection;

/*
 |--------------------------------------------------------------------------
 | models/ purpose
 |--------------------------------------------------------------------------
 | Data access objects for MongoDB collections and related domain behavior.
 */

class Listing
{
    private Collection $collection;

    public function __construct()
    {
        $this->collection = getMongoDatabase()->selectCollection('listings');
    }

    public function getLatest(int $limit = 20): array
    {
        $cursor = $this->collection->find(
            [],
            [
                'sort' => ['created_at' => -1],
                'limit' => $limit,
            ]
        );

        return iterator_to_array($cursor, false);
    }

    public function seedIfEmpty(): void
    {
        if ($this->collection->countDocuments() > 0) {
            return;
        }

        $this->collection->insertMany([
            [
                'title' => 'Experienced Housekeeper',
                'description' => '5 years experience in cleaning and home management.',
                'location' => 'Dhaka',
                'created_at' => new UTCDateTime(),
            ],
            [
                'title' => 'Family Looking for Nanny',
                'description' => 'Need daytime child care support for two kids.',
                'location' => 'Chittagong',
                'created_at' => new UTCDateTime(),
            ],
        ]);
    }
}
