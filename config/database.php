<?php

declare(strict_types=1);

use MongoDB\Client;

/*
 |--------------------------------------------------------------------------
 | config/ purpose
 |--------------------------------------------------------------------------
 | Shared infrastructure wiring such as MongoDB Atlas connection management.
 */

function getMongoClient(): Client
{
    static $client = null;

    if (!extension_loaded('mongodb')) {
        throw new RuntimeException('PHP extension ext-mongodb is not enabled. Enable it, then restart PHP/FPM.');
    }

    if (!class_exists(Client::class)) {
        throw new RuntimeException('MongoDB PHP library is missing. Run composer install in the project root.');
    }

    if ($client instanceof Client) {
        return $client;
    }

    $config = appConfig();

    if (empty($config['mongodb_uri'])) {
        throw new RuntimeException('MONGODB_URI is missing. Configure it before starting the app.');
    }

    try {
        // Reuse one client instance per request/runtime to avoid connection churn.
        $client = new Client($config['mongodb_uri'], [
            'appname' => $config['app_name'],
        ]);
    } catch (Throwable $exception) {
        error_log('MongoDB client initialization failed: ' . $exception->getMessage());
        throw new RuntimeException('Cannot connect to database right now.');
    }

    return $client;
}

function getMongoDatabase()
{
    $config = appConfig();
    return getMongoClient()->selectDatabase($config['mongodb_db']);
}
