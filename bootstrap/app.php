<?php

// Bootstrap the application
require_once __DIR__ . '/../vendor/autoload.php';

// Load configuration
$config = [
    'app' => require __DIR__ . '/../config/app.php',
    'database' => require __DIR__ . '/../config/database.php',
];

// Initialize database connection
$db = new Database($config['database']);

// Initialize the application
$app = new App($config, $db);

return $app;
