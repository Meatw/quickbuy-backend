<?php

// Application configuration
return [
    'name' => 'QuickBuy',
    'version' => '1.0.0',
    'debug' => true,
    'url' => 'http://localhost',
    'timezone' => 'UTC',
    'jwt_secret' => 'your_jwt_secret_key_here', // Change this in production
    'jwt_expiration' => 3600, // 1 hour
];
