<?php
require 'vendor/autoload.php';
require_once 'config/app.php';
require_once 'config/database.php';

$db = getMongoDatabase();
$users = $db->selectCollection('users');

$cursor = $users->find([], ['limit' => 10]);
$allUsers = iterator_to_array($cursor);

if (empty($allUsers)) {
    echo "NO USERS FOUND IN DATABASE.\n";
} else {
    echo "Found " . count($allUsers) . " users:\n";
    foreach ($allUsers as $u) {
        echo "---------------------------\n";
        echo "ID: " . $u['_id'] . "\n";
        echo "Email: " . $u['email'] . "\n";
        echo "Verified: " . ($u['is_verified'] ? 'Yes' : 'No') . "\n";
        echo "Role: " . $u['role'] . "\n";
        echo "Token Hash: " . ($u['verification_token'] ?? 'N/A') . "\n";

    }
}
