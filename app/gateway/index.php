<?php
/**
 * API Gateway - Main Entry Point
 * Routes requests to appropriate microservices
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Load shared utilities
require_once __DIR__ . '/../shared/Request.php';
require_once __DIR__ . '/../shared/Response.php';
require_once __DIR__ . '/../shared/Router.php';

// Load configuration
$config = require __DIR__ . '/../../config.php';

// Set timezone
date_default_timezone_set($config['app']['timezone']);

// Initialize request
$request = new Request();

// Create router with base path
$router = new Router('/api');

// Health check endpoint
$router->get('/health', function($request) use ($config) {
    Response::success('API Gateway is running', [
        'version' => $config['app']['version'],
        'timestamp' => date('Y-m-d H:i:s'),
        'services' => [
            'auth' => $config['services']['auth']['enabled'] ? 'available' : 'disabled',
            'configuration' => $config['services']['configuration']['enabled'] ? 'available' : 'disabled',
            'database' => $config['services']['database']['enabled'] ? 'available' : 'disabled',
        ]
    ]);
});

// List available services
$router->get('/services', function($request) {
    Response::success('Available services', [
        'services' => [
            [
                'name' => 'Auth Service',
                'endpoint' => '/api/auth',
                'description' => 'Authentication and authorization',
                'methods' => ['POST /api/auth/register', 'POST /api/auth/login', 'POST /api/auth/verify', 'POST /api/auth/refresh']
            ],
            [
                'name' => 'Configuration Service',
                'endpoint' => '/api/config',
                'description' => 'Configuration management',
                'methods' => ['GET /api/config', 'GET /api/config/:key', 'POST /api/config', 'PUT /api/config/:key']
            ],
            [
                'name' => 'Database Service',
                'endpoint' => '/api/db',
                'description' => 'Database operations',
                'methods' => ['GET /api/db/tables', 'POST /api/db/query', 'GET /api/db/health']
            ]
        ]
    ]);
});

// Route to Auth Service
$router->post('/auth/register', function($request) {
    return forward($request, 'auth-service', 'register');
});

$router->post('/auth/login', function($request) {
    return forward($request, 'auth-service', 'login');
});

$router->post('/auth/verify', function($request) {
    return forward($request, 'auth-service', 'verify');
});

$router->post('/auth/refresh', function($request) {
    return forward($request, 'auth-service', 'refresh');
});

$router->get('/auth/user', function($request) {
    return forward($request, 'auth-service', 'user');
});

// Route to Configuration Service
$router->get('/config', function($request) {
    return forward($request, 'configuration-service', 'getAll');
});

$router->get('/config/:key', function($request, $key) {
    return forward($request, 'configuration-service', 'get', ['key' => $key]);
});

$router->post('/config', function($request) {
    return forward($request, 'configuration-service', 'set');
});

$router->put('/config/:key', function($request, $key) {
    return forward($request, 'configuration-service', 'update', ['key' => $key]);
});

// Route to Database Service
$router->get('/db/health', function($request) {
    return forward($request, 'database-service', 'health');
});

$router->get('/db/tables', function($request) {
    return forward($request, 'database-service', 'tables');
});

$router->post('/db/query', function($request) {
    return forward($request, 'database-service', 'query');
});

/**
 * Forward request to specific microservice
 */
function forward($request, $service, $action, $params = []) {
    $servicePath = __DIR__ . "/../services/$service/index.php";
    
    if (!file_exists($servicePath)) {
        Response::error("Service '$service' not found", 503);
    }
    
    // Set service context
    $_ENV['SERVICE_ACTION'] = $action;
    $_ENV['SERVICE_PARAMS'] = json_encode($params);
    
    // Include and execute service
    require $servicePath;
}

// Dispatch request
$router->dispatch($request);
