<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/ServantProfile.php';

$db = getMongoDatabase();
$profiles = $db->selectCollection('servant_profiles')->find([
    '$or' => [
        ['experience' => ['$exists' => false]],
        ['skills' => ['$exists' => false]],
        ['hourly_rate' => ['$exists' => false]],
        ['availability' => ['$exists' => false]],
        ['experience' => ''],
        ['skills' => []],
        ['hourly_rate' => ''],
        ['availability' => '']
    ]
]);

$count = 0;
foreach ($profiles as $profile) {
    echo "Profile for User ID: " . $profile['user_id'] . "\n";
    echo "Missing/Empty fields: ";
    $missing = [];
    if (!isset($profile['experience']) || $profile['experience'] === '') $missing[] = 'experience';
    if (!isset($profile['skills']) || empty($profile['skills'])) $missing[] = 'skills';
    if (!isset($profile['hourly_rate']) || $profile['hourly_rate'] === '') $missing[] = 'hourly_rate';
    if (!isset($profile['availability']) || $profile['availability'] === '') $missing[] = 'availability';
    echo implode(', ', $missing) . "\n\n";
    $count++;
}

echo "Total profiles with missing/empty fields: $count\n";
