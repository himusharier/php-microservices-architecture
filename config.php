<?php
/**
 * Global Configuration File
 * Shared across all microservices
 */

return [
    'app' => [
        'name' => 'Microservice Framework',
        'version' => '1.0.0',
        'environment' => 'development', // development, production
        'timezone' => 'UTC',
        'base_url' => rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/'),
    ],
    
    'database' => [
        'host' => 'localhost',
        'port' => '3306',
        'database' => 'microservice_db',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8mb4',
    ],
    
    'jwt' => [
        'secret' => 'your-secret-key-change-this-in-production',
        'algorithm' => 'HS256',
        'expiration' => 3600, // 1 hour in seconds
    ],
    
    'services' => [
        'auth' => [
            'enabled' => true,
            'port' => null, // Using Apache, not separate ports
        ],
        'configuration' => [
            'enabled' => true,
            'port' => null,
        ],
        'database' => [
            'enabled' => true,
            'port' => null,
        ],
    ],
    
    'cors' => [
        'allowed_origins' => ['*'],
        'allowed_methods' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
        'allowed_headers' => ['Content-Type', 'Authorization'],
    ],
];
