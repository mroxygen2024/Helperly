<?php

declare(strict_types=1);

use MongoDB\Client;
use MongoDB\Database;

/*
 |--------------------------------------------------------------------------
 | config/db.php
 |--------------------------------------------------------------------------
 | Reusable MongoDB Atlas connection file.
 | - Reads the connection string from MONGO_URI
 | - Connects to the home_services database
 | - Returns a MongoDB\Database instance
 */

// Composer autoload is required for mongodb/mongodb classes.
$autoloadPath = __DIR__ . '/../vendor/autoload.php';
if (!file_exists($autoloadPath)) {
    throw new RuntimeException('Composer autoload file not found. Run: composer install');
}

require_once $autoloadPath;

$mongoUri = getenv('MONGO_URI');
if ($mongoUri === false || trim($mongoUri) === '') {
    throw new RuntimeException('Environment variable MONGO_URI is not set.');
}

try {
    // Single client creation in this module keeps DB access centralized and reusable.
    $client = new Client($mongoUri, [
        'appname' => 'home_services_app',
    ]);

    /** @var Database $db */
    $db = $client->selectDatabase('home_services');

    return $db;
} catch (Throwable $exception) {
    // Log detailed error server-side, expose generic message to callers.
    error_log('MongoDB connection error: ' . $exception->getMessage());
    throw new RuntimeException('Unable to connect to MongoDB at this time.');
}
