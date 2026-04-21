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
                'role' => 'service_provider',
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
                'skills' => $skillsPool[$i % count($skillsPool)],
                'experience' => $experience[$i % count($experience)],
                'location' => $locations[$i % count($locations)],
                'availability' => $availability[$i % count($availability)],
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
        'role' => 'service_provider',
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

echo "Users upserted(new): {$upsertedUsers}\n";
echo "Users updated(existing): {$updatedUsers}\n";
echo "Profiles upserted(new): {$upsertedProfiles}\n";
echo "Profiles updated(existing): {$updatedProfiles}\n";
echo "Total dummy servant users: {$totalDummyUsers}\n";
echo "Total dummy servant profiles: {$totalDummyProfiles}\n";
