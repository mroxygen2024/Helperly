<?php
require 'vendor/autoload.php';
require_once 'config/app.php';
require_once 'config/database.php';

$client = getMongoClient();
$config = appConfig();
$dbName = $config['mongodb_db'];

echo "Databases found:\n";
foreach ($client->listDatabases() as $db) {
    echo "- " . $db->getName() . "\n";
}

$db = $client->selectDatabase($dbName);
echo "\nCollections in {$dbName}:\n";
foreach ($db->listCollections() as $col) {
    echo "- " . $col->getName() . "\n";
}
