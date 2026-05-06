<?php
require 'vendor/autoload.php';
require_once 'config/app.php';
require_once 'config/database.php';

$db = getMongoDatabase();
$users = $db->selectCollection('users');

$email = 'fuadsano460@gmail.com';

$result = $users->updateOne(
    ['email' => $email],
    ['$set' => ['is_verified' => true, 'verified_at' => new MongoDB\BSON\UTCDateTime()]]
);

if ($result->getMatchedCount() > 0) {
    echo "User $email has been successfully verified in the database.\n";
} else {
    echo "User $email not found. Searching for all unverified users...\n";
    $unverified = $users->find(['is_verified' => false]);
    $count = 0;
    foreach ($unverified as $user) {
        $users->updateOne(['_id' => $user['_id']], ['$set' => ['is_verified' => true]]);
        echo "Verified: " . $user['email'] . "\n";
        $count++;
    }
    if ($count === 0) {
        echo "No unverified users found.\n";
    } else {
        echo "Successfully verified $count user(s).\n";
    }
}
