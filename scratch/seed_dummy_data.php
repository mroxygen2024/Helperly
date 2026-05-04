<?php
// scratch/seed_dummy_data.php

require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once dirname(__DIR__) . '/config/app.php';
require_once dirname(__DIR__) . '/config/helpers.php';
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/models/User.php';
require_once dirname(__DIR__) . '/models/ServantProfile.php';
require_once dirname(__DIR__) . '/models/Job.php';
require_once dirname(__DIR__) . '/models/JobApplication.php';

use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;

$userModel = new User();
$profileModel = new ServantProfile();
$jobModel = new Job();
$appModel = new JobApplication();

$password = 'Password123';

echo "Seeding dummy data...\n";

// 1. Create Admin
$adminEmail = 'admin@helperly.local';
if (!$userModel->findUserByEmail($adminEmail)) {
    $userId = $userModel->createUser('Admin User', $adminEmail, '+251900000000', $password, 'administrator', '');
    getMongoDatabase()->selectCollection('users')->updateOne(
        ['_id' => new ObjectId($userId)],
        ['$set' => ['is_verified' => true]]
    );
    echo "Admin created: $adminEmail / $password\n";
} else {
    echo "Admin already exists.\n";
}

// 2. Create Parent
$parentEmail = 'abe.parent@example.com';
$parentId = null;
if (!$userModel->findUserByEmail($parentEmail)) {
    $parentId = $userModel->createUser('Abebe Parent', $parentEmail, '+251911223344', $password, 'parent', '');
    getMongoDatabase()->selectCollection('users')->updateOne(
        ['_id' => new ObjectId($parentId)],
        ['$set' => ['is_verified' => true]]
    );
    echo "Parent created: $parentEmail / $password\n";

    // Add an open job
    $jobModel->createJob(
        $parentId,
        (new DateTime('+1 day'))->format('Y-m-d\TH:i'), // time
        '4', // duration
        'Home Cleaning', // serviceType
        'Addis Ababa, Bole', // location
        'Please bring your own supplies.', // instructions
        null, // selectedProviderId
        200.0, // rate
        800.0 // totalCost
    );
    echo "Parent open job created.\n";
} else {
    $user = $userModel->findUserByEmail($parentEmail);
    $parentId = (string)$user['_id'];
    echo "Parent already exists.\n";
}

// 3. Create Provider
$providerEmail = 'cha.provider@example.com';
$providerId = null;
if (!$userModel->findUserByEmail($providerEmail)) {
    $providerId = $userModel->createUser('Chala Provider', $providerEmail, '+251922334455', $password, 'provider', '');
    getMongoDatabase()->selectCollection('users')->updateOne(
        ['_id' => new ObjectId($providerId)],
        ['$set' => ['is_verified' => true]]
    );
    echo "Provider created: $providerEmail / $password\n";

    // Create Profile
    $profileModel->createOrUpdateProfile(
        $providerId,
        'Chala Provider',
        'ID12345678',
        28,
        'Male',
        ['Cleaning', 'Cooking', 'Gardening'],
        '5+ years',
        'Addis Ababa',
        'Weekdays, 8 AM - 5 PM',
        '200',
        'ETB',
        'https://ui-avatars.com/api/?name=Chala+Provider&background=random',
        '', '', ''
    );
    $profileModel->updateVerificationStatus($providerId, 'approved', 'Verified by seed script');
    echo "Provider profile created and approved.\n";
} else {
    $user = $userModel->findUserByEmail($providerEmail);
    $providerId = (string)$user['_id'];
    echo "Provider already exists.\n";
}

// 4. Create an Active Job (Parent + Provider)
if ($parentId && $providerId) {
    $jobId = $jobModel->createJob(
        $parentId,
        (new DateTime('+2 days'))->format('Y-m-d\TH:i'),
        '3',
        'Cooking',
        'Addis Ababa, Piazza',
        'Ethiopian traditional food preparation.',
        $providerId,
        250.0,
        750.0
    );
    echo "Active job created between Parent and Provider.\n";
}

echo "Seeding completed successfully.\n";
