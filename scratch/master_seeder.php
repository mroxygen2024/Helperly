<?php

declare(strict_types=1);

require 'vendor/autoload.php';
require_once 'config/app.php';
require_once 'config/helpers.php';
require_once 'config/database.php';

// Import models
require_once 'models/User.php';
require_once 'models/ServantProfile.php';
require_once 'models/EmployerProfile.php';
require_once 'models/Job.php';
require_once 'models/JobApplication.php';
require_once 'models/Payment.php';
require_once 'models/Review.php';
require_once 'models/Message.php';

use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;

echo "--- Helperly Master Seeder ---\n";

$db = getMongoDatabase();

// 1. Clear existing data
echo "Clearing existing data...\n";
$collections = ['users', 'servant_profiles', 'employer_profiles', 'jobs', 'job_applications', 'payments', 'reviews', 'messages'];
foreach ($collections as $colName) {
    $db->selectCollection($colName)->deleteMany([]);
}


$userModel = new User();
$servantProfileModel = new ServantProfile();
$employerProfileModel = new EmployerProfile();
$jobModel = new Job();
$applicationModel = new JobApplication();
$paymentModel = new Payment();
$reviewModel = new Review();

// 2. Create Admin
echo "Creating Admin...\n";
$adminId = $userModel->createUser(
    'System Admin',
    'admin@helperly.local',
    '+8801700000000',
    'password123',
    'administrator',
    'verify-admin'
);
$userModel->verifyUserByToken('verify-admin');

// 3. Create Parents
echo "Creating Parents...\n";
$parents = [
    [
        'name' => 'John Doe',
        'email' => 'john@parent.local',
        'phone' => '+8801811111111',
        'address' => 'House 12, Road 5, Dhanmondi',
        'location' => 'Dhaka',
        'kids' => [2, 5],
        'pref' => 'Reliable, Non-smoker'
    ],
    [
        'name' => 'Sarah Khan',
        'email' => 'sarah@parent.local',
        'phone' => '+8801822222222',
        'address' => 'Flat 4B, Skyview Tower, Banani',
        'location' => 'Dhaka',
        'kids' => [3],
        'pref' => 'English speaking, Gentle'
    ],
    [
        'name' => 'Michael Smith',
        'email' => 'michael@parent.local',
        'phone' => '+8801833333333',
        'address' => 'Agrabad, CDA Area',
        'location' => 'Chittagong',
        'kids' => [1, 4, 7],
        'pref' => 'Experienced with toddlers'
    ]
];

$parentIds = [];
foreach ($parents as $p) {
    $id = $userModel->createUser($p['name'], $p['email'], $p['phone'], 'password123', 'parent', 'verify-' . $p['email']);
    $userModel->verifyUserByToken('verify-' . $p['email']);
    $employerProfileModel->createOrUpdateProfile(
        $id,
        $p['address'],
        $p['location'],
        ['+8801999999999'],
        $p['kids'],
        $p['pref']
    );
    $parentIds[] = $id;
}

// 4. Create Providers
echo "Creating Providers...\n";
$providers = [
    [
        'name' => 'Rahima Begum',
        'email' => 'rahima@provider.local',
        'phone' => '+8801911111111',
        'skills' => 'Cleaning, Cooking, Laundry',
        'exp' => '5 years',
        'rate' => '300',
        'loc' => 'Dhaka',
        'status' => 'approved',
        'bio' => 'Experienced housekeeper with great references.',
        'photo' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=200&h=200&fit=crop'
    ],
    [
        'name' => 'Fatima Khatun',
        'email' => 'fatima@provider.local',
        'phone' => '+8801922222222',
        'skills' => 'Nanny, Child Care, Tutoring',
        'exp' => '10 years',
        'rate' => '500',
        'loc' => 'Dhaka',
        'status' => 'approved',
        'bio' => 'Kind and patient nanny looking for a long-term family.',
        'photo' => 'https://images.unsplash.com/photo-1548142813-c348350df52b?w=200&h=200&fit=crop'
    ],
    [
        'name' => 'Sumon Ahmed',
        'email' => 'sumon@provider.local',
        'phone' => '+8801933333333',
        'skills' => 'Gardening, Driving, Handyman',
        'exp' => '3 years',
        'rate' => '400',
        'loc' => 'Chittagong',
        'status' => 'approved',
        'bio' => 'Strong and dedicated worker available for home maintenance.',
        'photo' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=200&h=200&fit=crop'
    ],
    [
        'name' => 'Jannat Ara',
        'email' => 'jannat@provider.local',
        'phone' => '+8801944444444',
        'skills' => 'Cooking, Baby Sitting',
        'exp' => '2 years',
        'rate' => '250',
        'loc' => 'Dhaka',
        'status' => 'pending',
        'bio' => 'Quick learner and very hardworking.',
        'photo' => 'https://images.unsplash.com/photo-1554151228-14d9def656e4?w=200&h=200&fit=crop'
    ],
    [
        'name' => 'Abdul Karim',
        'email' => 'abdul@provider.local',
        'phone' => '+8801955555555',
        'skills' => 'Security Guard, Driver',
        'exp' => '7 years',
        'rate' => '450',
        'loc' => 'Sylhet',
        'status' => 'pending',
        'bio' => 'Professional driver with valid license.',
        'photo' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=200&h=200&fit=crop'
    ]
];

$providerIds = [];
$approvedProviderIds = [];
foreach ($providers as $p) {
    $id = $userModel->createUser($p['name'], $p['email'], $p['phone'], 'password123', 'service_provider', 'verify-' . $p['email']);
    $userModel->verifyUserByToken('verify-' . $p['email']);
    $servantProfileModel->createOrUpdateProfile(
        $id,
        $p['name'],
        'NID-' . rand(100000, 999999),
        rand(25, 45),
        'female', // simplified
        $p['skills'],
        $p['exp'],
        $p['loc'],
        'Full-time, Weekend',
        $p['rate'],
        $p['photo'],
        'https://placehold.co/600x400?text=Front',
        'https://placehold.co/600x400?text=Back',
        'https://placehold.co/600x400?text=Selfie'
    );

    if ($p['status'] === 'approved') {
        $servantProfileModel->updateVerificationStatus($id, 'approved', '');
        $approvedProviderIds[] = $id;
    }

    $providerIds[] = $id;
}

// 5. Create Jobs
echo "Creating Jobs...\n";

// -- Open Job 1 --
$jobModel->createJob(
    $parentIds[0],
    date('Y-m-d H:i:s', strtotime('+2 days')),
    '4 hours',
    'Cleaning',
    'Dhanmondi',
    'Deep cleaning of 3 bedrooms and 2 bathrooms.'
);
$openJob = $db->jobs->findOne(['instructions' => 'Deep cleaning of 3 bedrooms and 2 bathrooms.']);
$jobId1 = (string) $openJob['_id'];
$applicationModel->createApplication($jobId1, $approvedProviderIds[0]);
$applicationModel->createApplication($jobId1, $providerIds[3]); // pending provider also applying

// -- Open Job 2 --
$jobModel->createJob(
    $parentIds[1],
    date('Y-m-d H:i:s', strtotime('+1 week')),
    'Full Day',
    'Nanny',
    'Banani',
    'Need a nanny for my 3-year-old child from 9 AM to 6 PM.'
);

// -- Active Job (John Doe + Rahima) --
$jobModel->createJob(
    $parentIds[0],
    date('Y-m-d H:i:s', strtotime('-1 day')),
    '3 hours',
    'Housework',
    'Dhanmondi',
    'Ironing and kitchen organization.',
    $approvedProviderIds[0],
    300,
    900
);

// -- Completed Job (Sarah Khan + Fatima) --
$jobModel->createJob(
    $parentIds[1],
    date('Y-m-d H:i:s', strtotime('-5 days')),
    '5 hours',
    'Child Care',
    'Banani',
    'Temporary care while I was at a meeting.',
    $approvedProviderIds[1],
    500,
    2500
);
$completedJob = $db->jobs->findOne(['instructions' => 'Temporary care while I was at a meeting.']);
$jobId3 = (string) $completedJob['_id'];
$db->jobs->updateOne(['_id' => new ObjectId($jobId3)], ['$set' => ['status' => 'completed', 'parent_confirmed' => true, 'provider_confirmed' => true]]);

// Create Payment for Completed Job
$paymentModel->createPayment($jobId3, 2500);
$paymentModel->updateStatus($jobId3, 'paid');

// Create Review for Completed Job
$reviewModel->createReview($jobId3, $approvedProviderIds[1], $parentIds[1], 5, 'Fatima was amazing with my son! Very professional.');

// Update Fatima's rating
$servantProfileModel->updateCachedRating($approvedProviderIds[1], 5.0, 1);

// -- Messages (John Doe <-> Rahima) --
echo "Creating Messages...\n";
$messageModel = new Message();
$activeJob = $db->jobs->findOne(['instructions' => 'Ironing and kitchen organization.']);
if ($activeJob) {
    $activeJobId = (string) $activeJob['_id'];
    $messageModel->sendMessage($parentIds[0], $approvedProviderIds[0], $activeJobId, 'Hello Rahima, are you available tomorrow?');
    $messageModel->sendMessage($approvedProviderIds[0], $parentIds[0], $activeJobId, 'Yes, I can come at 10 AM.');
    $messageModel->sendMessage($parentIds[0], $approvedProviderIds[0], $activeJobId, 'Great, see you then.');
}

// -- Completed Job (Michael + Sumon) --
$jobModel->createJob(
    $parentIds[2],
    date('Y-m-d H:i:s', strtotime('-2 weeks')),
    '2 hours',
    'Gardening',
    'Chittagong',
    'Trimming the hedges.',
    $approvedProviderIds[2],
    400,
    800
);
$completedJob2 = $db->jobs->findOne(['instructions' => 'Trimming the hedges.']);
$jobId4 = (string) $completedJob2['_id'];
$db->jobs->updateOne(['_id' => new ObjectId($jobId4)], ['$set' => ['status' => 'completed', 'parent_confirmed' => true, 'provider_confirmed' => true]]);
$paymentModel->createPayment($jobId4, 800);
$paymentModel->updateStatus($jobId4, 'paid');
$reviewModel->createReview($jobId4, $approvedProviderIds[2], $parentIds[2], 4, 'Good work, very punctual.');
$servantProfileModel->updateCachedRating($approvedProviderIds[2], 4.0, 1);

echo "Seed data created successfully!\n";
echo "Admin: admin@helperly.local / password123\n";
echo "Parents: john@parent.local, sarah@parent.local, michael@parent.local / password123\n";
echo "Providers: rahima@provider.local, fatima@provider.local, sumon@provider.local / password123\n";
