<?php
require 'vendor/autoload.php';
require_once 'config/app.php';
require_once 'config/database.php';

$client = getMongoClient();
echo "Databases found:\n";
foreach ($client->listDatabases() as $db) {
    echo "- " . $db->getName() . "\n";
}

$db = $client->selectDatabase('servant_marketplace');
echo "\nCollections in servant_marketplace:\n";
foreach ($db->listCollections() as $col) {
    echo "- " . $col->getName() . "\n";
}
