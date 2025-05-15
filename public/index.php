<?php

// Entry point for the application
$app = require_once __DIR__ . '/../bootstrap/app.php';

// Handle the request
$app->handleRequest();
