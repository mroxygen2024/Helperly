<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/ServantProfile.php';

$servantProfiles = new ServantProfile();
$profiles = getMongoDatabase()->selectCollection('servant_profiles')->find([], ['limit' => 1]);

foreach ($profiles as $profile) {
    echo json_encode($profile, JSON_PRETTY_PRINT) . PHP_EOL;
}
