<?php
require 'vendor/autoload.php';
require_once 'config/app.php';
require_once 'config/database.php';

$db = getMongoDatabase();
$profiles = $db->selectCollection('servant_profiles')->find();

echo "Checking ID URLs in servant_profiles:\n";
foreach ($profiles as $p) {
    echo "User: " . $p['user_id'] . "\n";
    echo "  Front: " . ($p['fayda_id_front_url'] ?? 'MISSING') . "\n";
    echo "  Back: " . ($p['fayda_id_back_url'] ?? 'MISSING') . "\n";
    echo "  Selfie: " . ($p['selfie_url'] ?? 'MISSING') . "\n";
    echo "  Status: " . ($p['verification_status'] ?? 'MISSING') . "\n";
}
