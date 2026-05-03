<?php

declare(strict_types=1);

/**
 * config/db.php
 *
 * Standalone MongoDB connection file for CLI scripts and background workers.
 * Loads the main app configuration to ensure environment consistency.
 */

require_once __DIR__ . '/app.php';
require_once __DIR__ . '/database.php';

try {
    // getMongoDatabase() uses the centralized configuration in app.php
    return getMongoDatabase();
} catch (Throwable $exception) {
    error_log('MongoDB connection error in standalone loader: ' . $exception->getMessage());
    throw new RuntimeException('Unable to connect to MongoDB. Check your .env file and network.');
}
