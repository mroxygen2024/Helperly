<?php
declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php';

$db = getMongoDatabase();
$users = $db->selectCollection('users');
$profiles = $db->selectCollection('servant_profiles');

$skillsPool = [
    ['Cleaning', 'Laundry'],
    ['Cooking', 'Housekeeping'],
    ['Child Care', 'Cooking'],
    ['Elder Care', 'Medication Reminder'],
    ['Gardening', 'Cleaning'],
    ['Driving', 'Errands'],
    ['Pet Care', 'Cleaning'],
    ['Ironing', 'Laundry'],
    ['Baby Sitting', 'Housekeeping'],
    ['Cooking', 'Meal Prep'],
];

$locations = ['Dhaka', 'Chattogram', 'Khulna', 'Sylhet', 'Rajshahi', 'Barishal'];
$availability = ['Full-time', 'Part-time', 'Weekends', 'Morning shift', 'Evening shift'];
$experience = ['1 year', '2 years', '3 years', '4 years', '5+ years'];

$upsertedUsers = 0;
$updatedUsers = 0;
$upsertedProfiles = 0;
$updatedProfiles = 0;

$roleAccounts = [
    [
        'name' => 'Demo Parent',
        'email' => 'demo.parent@example.com',
        'phone' => '+8801800000001',
        'role' => 'parent',
        'password' => 'DemoPass123',
    ],
    [
        'name' => 'Demo Service Provider',
        'email' => 'demo.provider@example.com',
        'phone' => '+8801800000002',
        'role' => 'provider',
        'password' => 'DemoPass123',
    ],
    [
        'name' => 'Demo Administrator',
        'email' => 'demo.admin@example.com',
        'phone' => '+8801800000003',
        'role' => 'administrator',
        'password' => 'DemoPass123',
    ],
];

for ($i = 1; $i <= 30; $i++) {
    $name = sprintf('Demo Servant %02d', $i);
    $email = sprintf('dummy.servant%02d@example.com', $i);
    $phone = sprintf('+8801700%04d', $i);
    $now = new MongoDB\BSON\UTCDateTime();

    $userResult = $users->updateOne(
        ['email' => $email],
        [
            '$set' => [
                'name' => $name,
                'email' => $email,
                'role' => 'provider',
                'phone' => $phone,
                'updated_at' => $now,
            ],
            '$setOnInsert' => [
                'password_hash' => password_hash('DemoPass123', PASSWORD_DEFAULT),
                'created_at' => $now,
            ],
        ],
        ['upsert' => true]
    );

    if ($userResult->getUpsertedCount() > 0) {
        $upsertedUsers++;
    } elseif ($userResult->getMatchedCount() > 0) {
        $updatedUsers++;
    }

    $userDoc = $users->findOne(['email' => $email], ['projection' => ['_id' => 1]]);
    if (!$userDoc || !isset($userDoc['_id'])) {
        throw new RuntimeException('Unable to resolve user id for ' . $email);
    }

    $userId = $userDoc['_id'];

    $profileResult = $profiles->updateOne(
        ['user_id' => $userId],
        [
            '$set' => [
                'full_name' => $name,
                'national_id' => 'NID-' . rand(100000, 999999),
                'age' => rand(18, 65),
                'gender' => ($i % 2 === 0) ? 'female' : 'male',
                'skills' => $skillsPool[$i % count($skillsPool)],
                'experience' => $experience[$i % count($experience)],
                'location' => $locations[$i % count($locations)],
                'availability' => $availability[$i % count($availability)],
                'rate' => (string)(200 + ($i * 10)),
                'profile_photo' => 'https://placehold.co/200x200?text=' . urlencode($name),
                'fayda_id_front_url' => 'https://placehold.co/600x400?text=Front',
                'fayda_id_back_url' => 'https://placehold.co/600x400?text=Back',
                'selfie_url' => 'https://placehold.co/600x400?text=Selfie',
                'verification_status' => 'pending',
                'verification_notes' => '',
                'updated_at' => $now,
            ],
            '$setOnInsert' => [
                'user_id' => $userId,
                'created_at' => $now,
            ],
        ],
        ['upsert' => true]
    );

    if ($profileResult->getUpsertedCount() > 0) {
        $upsertedProfiles++;
    } elseif ($profileResult->getMatchedCount() > 0) {
        $updatedProfiles++;
    }
}

$cursor = $users->find(
    [
        'role' => 'provider',
        'email' => ['$regex' => '^dummy\\.servant\\d{2}@example\\.com$'],
    ],
    ['projection' => ['_id' => 1]]
);

$ids = [];
foreach ($cursor as $doc) {
    $ids[] = $doc['_id'];
}

$totalDummyUsers = count($ids);
$totalDummyProfiles = $profiles->countDocuments(['user_id' => ['$in' => $ids]]);

$upsertedRoleUsers = 0;
$updatedRoleUsers = 0;
$upsertedRoleProfiles = 0;
$updatedRoleProfiles = 0;

foreach ($roleAccounts as $account) {
    $now = new MongoDB\BSON\UTCDateTime();

    $roleUserResult = $users->updateOne(
        ['email' => $account['email']],
        [
            '$set' => [
                'name' => $account['name'],
                'email' => $account['email'],
                'phone' => $account['phone'],
                'role' => $account['role'],
                'is_verified' => true,
                'verified_at' => $now,
                'updated_at' => $now,
            ],
            '$unset' => [
                'verification_token' => '',
                'verification_sent_at' => '',
            ],
            '$setOnInsert' => [
                'password_hash' => password_hash($account['password'], PASSWORD_DEFAULT),
                'created_at' => $now,
            ],
        ],
        ['upsert' => true]
    );

    if ($roleUserResult->getUpsertedCount() > 0) {
        $upsertedRoleUsers++;
    } elseif ($roleUserResult->getMatchedCount() > 0) {
        $updatedRoleUsers++;
    }

    if ($account['role'] !== 'provider') {
        continue;
    }

    $providerDoc = $users->findOne(['email' => $account['email']], ['projection' => ['_id' => 1]]);
    if (!$providerDoc || !isset($providerDoc['_id'])) {
        throw new RuntimeException('Unable to resolve service provider id for ' . $account['email']);
    }

    $profileNow = new MongoDB\BSON\UTCDateTime();
    $providerProfileResult = $profiles->updateOne(
        ['user_id' => $providerDoc['_id']],
        [
            '$set' => [
                'full_name' => $account['name'],
                'national_id' => 'NID-' . rand(100000, 999999),
                'age' => rand(18, 65),
                'gender' => 'female',
                'skills' => ['Cleaning', 'Cooking'],
                'experience' => '3 years',
                'location' => 'Dhaka',
                'availability' => 'Full-time',
                'rate' => '350',
                'profile_photo' => 'https://placehold.co/200x200?text=' . urlencode($account['name']),
                'fayda_id_front_url' => 'https://placehold.co/600x400?text=Front',
                'fayda_id_back_url' => 'https://placehold.co/600x400?text=Back',
                'selfie_url' => 'https://placehold.co/600x400?text=Selfie',
                'verification_status' => 'approved',
                'verification_notes' => 'Auto-approved demo seed account.',
                'updated_at' => $profileNow,
            ],
            '$setOnInsert' => [
                'user_id' => $providerDoc['_id'],
                'created_at' => $profileNow,
            ],
        ],
        ['upsert' => true]
    );

    if ($providerProfileResult->getUpsertedCount() > 0) {
        $upsertedRoleProfiles++;
    } elseif ($providerProfileResult->getMatchedCount() > 0) {
        $updatedRoleProfiles++;
    }
}

echo "Users upserted(new): {$upsertedUsers}\n";
echo "Users updated(existing): {$updatedUsers}\n";
echo "Profiles upserted(new): {$upsertedProfiles}\n";
echo "Profiles updated(existing): {$updatedProfiles}\n";
echo "Total dummy servant users: {$totalDummyUsers}\n";
echo "Total dummy servant profiles: {$totalDummyProfiles}\n";
echo "Role users upserted(new): {$upsertedRoleUsers}\n";
echo "Role users updated(existing): {$updatedRoleUsers}\n";
echo "Role provider profiles upserted(new): {$upsertedRoleProfiles}\n";
echo "Role provider profiles updated(existing): {$updatedRoleProfiles}\n\n";

echo "Login credentials (all email-verified):\n";
foreach ($roleAccounts as $account) {
    echo "- role={$account['role']} | email={$account['email']} | password={$account['password']}\n";
}
