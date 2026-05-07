<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once dirname(__DIR__) . '/config/app.php';
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/config/helpers.php';

// Require all models
$modelsDir = dirname(__DIR__) . '/models';
foreach (glob($modelsDir . '/*.php') as $file) {
    require_once $file;
}

echo "Setting up MongoDB indexes...\n";

$models = [
    'User',
    'Job',
    'Message',
    'Service',
    'Review',
    'ServantProfile',
    'EmployerProfile',
    'JobApplication',
    'HireRequest',
    'Notification',
    'Payment'
];

foreach ($models as $modelName) {
    if (class_exists($modelName)) {
        echo "Ensuring indexes for {$modelName}...\n";
        $model = new $modelName();
        if (method_exists($model, 'ensureIndexes')) {
            $model->ensureIndexes();
        }
    }
}

echo "Database setup complete!\n";
