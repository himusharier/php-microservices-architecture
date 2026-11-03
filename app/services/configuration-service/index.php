<?php
/**
 * Configuration Service - Centralized Configuration Management
 */

// Load shared utilities
require_once __DIR__ . '/../../shared/Request.php';
require_once __DIR__ . '/../../shared/Response.php';
require_once __DIR__ . '/../../shared/Database.php';
require_once __DIR__ . '/../../shared/JWT.php';

// Load configuration
$config = require __DIR__ . '/../../../config.php';

// Initialize database
$db = Database::getInstance();

// Create configurations table if not exists
$db->execute("
    CREATE TABLE IF NOT EXISTS configurations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        config_key VARCHAR(100) UNIQUE NOT NULL,
        config_value TEXT,
        description VARCHAR(255),
        is_encrypted BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )
");

// Insert default configurations
$defaults = [
    ['app.name', 'Microservice Framework', 'Application name'],
    ['app.maintenance_mode', 'false', 'Maintenance mode flag'],
    ['app.max_upload_size', '10485760', 'Maximum upload size in bytes (10MB)'],
    ['security.password_min_length', '8', 'Minimum password length'],
    ['security.session_timeout', '3600', 'Session timeout in seconds'],
];

foreach ($defaults as $default) {
    $db->execute(
        "INSERT IGNORE INTO configurations (config_key, config_value, description) VALUES (?, ?, ?)",
        $default
    );
}

// Get action from gateway
$action = $_ENV['SERVICE_ACTION'] ?? 'unknown';
$params = json_decode($_ENV['SERVICE_PARAMS'] ?? '{}', true);

// Get request object
$request = new Request();

// Handle actions
switch ($action) {
    case 'getAll':
        handleGetAll($request, $db);
        break;
        
    case 'get':
        handleGet($request, $db, $params);
        break;
        
    case 'set':
        handleSet($request, $db);
        break;
        
    case 'update':
        handleUpdate($request, $db, $params);
        break;
        
    default:
        Response::error('Invalid action', 400);
}

/**
 * Get all configurations
 */
function handleGetAll($request, $db) {
    $configs = $db->fetchAll("SELECT * FROM configurations ORDER BY config_key");
    
    // Format configurations as key-value pairs
    $formatted = [];
    foreach ($configs as $config) {
        $formatted[$config['config_key']] = [
            'value' => $config['config_value'],
            'description' => $config['description'],
            'updated_at' => $config['updated_at']
        ];
    }
    
    Response::success('Configurations retrieved', [
        'configurations' => $formatted,
        'count' => count($configs)
    ]);
}

/**
 * Get specific configuration
 */
function handleGet($request, $db, $params) {
    $key = $params['key'] ?? null;
    
    if (!$key) {
        Response::error('Configuration key is required', 400);
    }
    
    $config = $db->fetchOne(
        "SELECT * FROM configurations WHERE config_key = ?",
        [$key]
    );
    
    if (!$config) {
        Response::notFound('Configuration not found');
    }
    
    Response::success('Configuration retrieved', [
        'key' => $config['config_key'],
        'value' => $config['config_value'],
        'description' => $config['description'],
        'updated_at' => $config['updated_at']
    ]);
}

/**
 * Set new configuration
 */
function handleSet($request, $db) {
    $body = $request->getBody();
    
    if (empty($body['key']) || !isset($body['value'])) {
        Response::error('Key and value are required', 400);
    }
    
    // Check if key already exists
    $existing = $db->fetchOne(
        "SELECT id FROM configurations WHERE config_key = ?",
        [$body['key']]
    );
    
    if ($existing) {
        Response::error('Configuration key already exists. Use PUT to update.', 409);
    }
    
    $db->execute(
        "INSERT INTO configurations (config_key, config_value, description) VALUES (?, ?, ?)",
        [
            $body['key'],
            $body['value'],
            $body['description'] ?? null
        ]
    );
    
    Response::success('Configuration created', [
        'key' => $body['key'],
        'value' => $body['value']
    ], 201);
}

/**
 * Update existing configuration
 */
function handleUpdate($request, $db, $params) {
    $key = $params['key'] ?? null;
    $body = $request->getBody();
    
    if (!$key) {
        Response::error('Configuration key is required', 400);
    }
    
    if (!isset($body['value'])) {
        Response::error('Value is required', 400);
    }
    
    // Check if key exists
    $existing = $db->fetchOne(
        "SELECT id FROM configurations WHERE config_key = ?",
        [$key]
    );
    
    if (!$existing) {
        Response::notFound('Configuration not found');
    }
    
    $db->execute(
        "UPDATE configurations SET config_value = ?, description = ? WHERE config_key = ?",
        [
            $body['value'],
            $body['description'] ?? null,
            $key
        ]
    );
    
    Response::success('Configuration updated', [
        'key' => $key,
        'value' => $body['value']
    ]);
}
