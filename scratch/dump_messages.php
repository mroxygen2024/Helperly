<?php
require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once dirname(__DIR__) . '/config/app.php';
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/models/Message.php';

$jobId = $argv[1] ?? '';
if (!$jobId) {
    echo "Usage: php dump_messages.php <job_id>\n";
    exit(1);
}

try {
    $msgModel = new Message();
    $messages = $msgModel->getMessagesByJobId($jobId);
    echo "Found " . count($messages) . " messages for job $jobId\n";
    foreach ($messages as $m) {
        echo " - [{$m['sender_id']}] {$m['message']}\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
