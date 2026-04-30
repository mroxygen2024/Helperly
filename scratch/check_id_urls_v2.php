<?php
require 'vendor/autoload.php';
require_once 'config/app.php';
require_once 'config/database.php';

$db = getMongoDatabase();
$users = $db->selectCollection('users')->find();
$emails = [];
foreach ($users as $u) {
    $emails[(string)$u['_id']] = $u['email'];
}

$profiles = $db->selectCollection('servant_profiles')->find();

echo "Checking ID URLs in servant_profiles:\n";
foreach ($profiles as $p) {
    $uid = (string)$p['user_id'];
    $email = $emails[$uid] ?? 'UNKNOWN';
    echo "User: $uid ($email)\n";
    echo "  Front: " . ($p['fayda_id_front_url'] ?? 'MISSING') . "\n";
    echo "  Back: " . ($p['fayda_id_back_url'] ?? 'MISSING') . "\n";
    echo "  Selfie: " . ($p['selfie_url'] ?? 'MISSING') . "\n";
    echo "  Status: " . ($p['verification_status'] ?? 'MISSING') . "\n";
}
