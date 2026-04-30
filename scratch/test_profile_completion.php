<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/ServantProfile.php';
require_once __DIR__ . '/../models/User.php';

use MongoDB\BSON\ObjectId;

$servantProfiles = new ServantProfile();
$users = new User();

// Create a dummy user
$userId = (string) new ObjectId();
echo "Testing with Dummy User ID: $userId\n";

$fields = [
    'fullName' => 'Test Provider',
    'nationalId' => 'NID12345',
    'age' => 25,
    'gender' => 'male',
    'skills' => 'Cooking, Cleaning',
    'experience' => '5 years',
    'location' => 'Dhaka',
    'availability' => 'Weekdays',
    'rate' => '500',
    'profilePhoto' => 'https://example.com/photo.jpg',
    'faydaIdFrontUrl' => 'https://example.com/front.jpg',
    'faydaIdBackUrl' => 'https://example.com/back.jpg',
    'selfieUrl' => 'https://example.com/selfie.jpg'
];

echo "Saving profile...\n";
$success = $servantProfiles->createOrUpdateProfile(
    $userId,
    $fields['fullName'],
    $fields['nationalId'],
    $fields['age'],
    $fields['gender'],
    $fields['skills'],
    $fields['experience'],
    $fields['location'],
    $fields['availability'],
    $fields['rate'],
    $fields['profilePhoto'],
    $fields['faydaIdFrontUrl'],
    $fields['faydaIdBackUrl'],
    $fields['selfieUrl']
);

if ($success) {
    echo "Profile saved successfully.\n";
} else {
    echo "Profile save failed.\n";
}

$profile = $servantProfiles->getProfileByUserId($userId);
echo "Retrieved Profile:\n";
echo json_encode($profile, JSON_PRETTY_PRINT) . PHP_EOL;

$isComplete = $servantProfiles->isProfileComplete($profile);
echo "Is Profile Complete? " . ($isComplete ? "YES" : "NO") . "\n";

// Test missing fields
echo "\nTesting missing 'rate' field...\n";
$incompleteProfile = $profile;
unset($incompleteProfile['rate']);
$isComplete = $servantProfiles->isProfileComplete($incompleteProfile);
echo "Is Profile Complete (missing rate)? " . ($isComplete ? "YES" : "NO") . "\n";

echo "\nTesting empty 'skills' field...\n";
$incompleteProfile = $profile;
$incompleteProfile['skills'] = [];
$isComplete = $servantProfiles->isProfileComplete($incompleteProfile);
echo "Is Profile Complete (empty skills)? " . ($isComplete ? "YES" : "NO") . "\n";

// Clean up
getMongoDatabase()->selectCollection('servant_profiles')->deleteOne(['user_id' => new ObjectId($userId)]);
echo "\nCleaned up dummy data.\n";
