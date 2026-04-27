<?php
require 'vendor/autoload.php';
require_once 'config/app.php';
require_once 'config/database.php';
require_once 'config/helpers.php';

$db = getMongoDatabase();
$users = $db->selectCollection('users');

$token = 'a646288c520faf4a51c6508607c3d723cfbb8fbbc60ede8dc9abc4ce5cdc0426';
$tokenHash = hashVerificationToken($token);

echo "Checking for token hash: $tokenHash\n";

$userByToken = $users->findOne(['verification_token' => $tokenHash]);

if ($userByToken) {
    echo "User found by token: " . $userByToken['email'] . "\n";
    echo "Is Verified: " . ($userByToken['is_verified'] ? 'Yes' : 'No') . "\n";
} else {
    echo "No user found with that verification token.\n";

    // Check all users
    echo "\nLast 5 users in DB:\n";
    $cursor = $users->find([], ['sort' => ['created_at' => -1], 'limit' => 5]);
    foreach ($cursor as $u) {
        echo "- Email: " . $u['email'] . " | Verified: " . ($u['is_verified'] ? 'Yes' : 'No') . " | Role: " . $u['role'] . "\n";
    }
}
