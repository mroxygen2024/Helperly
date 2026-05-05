<?php
require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once dirname(__DIR__) . '/config/app.php';
require_once dirname(__DIR__) . '/config/database.php';

try {
    $db = getMongoDatabase();
    $job = $db->selectCollection('jobs')->findOne([], ['sort' => ['created_at' => -1]]);
    if ($job) {
        echo (string)$job['_id'] . "\n";
    } else {
        echo "No jobs found\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
