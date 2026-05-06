<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../config/database.php';

echo "--- Field Rename Migration: hourly_rate -> rate ---\n";

$db = getMongoDatabase();

// 1. Migrate servant_profiles
echo "Migrating servant_profiles...\n";
$profileResult = $db->selectCollection('servant_profiles')->updateMany(
    ['hourly_rate' => ['$exists' => true]],
    ['$rename' => ['hourly_rate' => 'rate']]
);
echo "Profiles updated: " . $profileResult->getModifiedCount() . "\n";

// 2. Migrate jobs
echo "Migrating jobs...\n";
$jobResult = $db->selectCollection('jobs')->updateMany(
    ['hourly_rate' => ['$exists' => true]],
    ['$rename' => ['hourly_rate' => 'rate']]
);
echo "Jobs updated: " . $jobResult->getModifiedCount() . "\n";

echo "Migration complete!\n";
