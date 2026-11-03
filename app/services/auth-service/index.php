<?php
/**
 * Auth Service - User Authentication and Authorization
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

// Create users table if not exists
$db->execute("
    CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        full_name VARCHAR(100),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        last_login TIMESTAMP NULL,
        is_active BOOLEAN DEFAULT TRUE
    )
");

// Get action from gateway
$action = $_ENV['SERVICE_ACTION'] ?? 'unknown';
$params = json_decode($_ENV['SERVICE_PARAMS'] ?? '{}', true);

// Get request object
$request = new Request();

// Handle actions
switch ($action) {
    case 'register':
        handleRegister($request, $db, $config);
        break;
        
    case 'login':
        handleLogin($request, $db, $config);
        break;
        
    case 'verify':
        handleVerify($request, $config);
        break;
        
    case 'refresh':
        handleRefresh($request, $db, $config);
        break;
        
    case 'user':
        handleGetUser($request, $db, $config);
        break;
        
    default:
        Response::error('Invalid action', 400);
}

/**
 * Register new user
 */
function handleRegister($request, $db, $config) {
    $body = $request->getBody();
    
    // Validate input
    if (empty($body['username']) || empty($body['email']) || empty($body['password'])) {
        Response::error('Username, email, and password are required', 400);
    }
    
    // Validate email
    if (!filter_var($body['email'], FILTER_VALIDATE_EMAIL)) {
        Response::error('Invalid email format', 400);
    }
    
    // Check if user exists
    $existing = $db->fetchOne(
        "SELECT id FROM users WHERE username = ? OR email = ?",
        [$body['username'], $body['email']]
    );
    
    if ($existing) {
        Response::error('Username or email already exists', 409);
    }
    
    // Hash password
    $passwordHash = password_hash($body['password'], PASSWORD_BCRYPT);
    
    // Insert user
    $db->execute(
        "INSERT INTO users (username, email, password, full_name) VALUES (?, ?, ?, ?)",
        [
            $body['username'],
            $body['email'],
            $passwordHash,
            $body['full_name'] ?? null
        ]
    );
    
    $userId = $db->lastInsertId();
    
    // Generate JWT token
    $token = JWT::encode(
        [
            'user_id' => $userId,
            'username' => $body['username'],
            'email' => $body['email']
        ],
        $config['jwt']['secret'],
        $config['jwt']['expiration']
    );
    
    Response::success('User registered successfully', [
        'user_id' => $userId,
        'username' => $body['username'],
        'email' => $body['email'],
        'token' => $token
    ], 201);
}

/**
 * Login user
 */
function handleLogin($request, $db, $config) {
    $body = $request->getBody();
    
    // Validate input
    if (empty($body['login']) || empty($body['password'])) {
        Response::error('Login and password are required', 400);
    }
    
    // Find user by username or email
    $user = $db->fetchOne(
        "SELECT * FROM users WHERE username = ? OR email = ?",
        [$body['login'], $body['login']]
    );
    
    if (!$user || !password_verify($body['password'], $user['password'])) {
        Response::error('Invalid credentials', 401);
    }
    
    if (!$user['is_active']) {
        Response::error('Account is disabled', 403);
    }
    
    // Update last login
    $db->execute(
        "UPDATE users SET last_login = NOW() WHERE id = ?",
        [$user['id']]
    );
    
    // Generate JWT token
    $token = JWT::encode(
        [
            'user_id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email']
        ],
        $config['jwt']['secret'],
        $config['jwt']['expiration']
    );
    
    Response::success('Login successful', [
        'user' => [
            'id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'full_name' => $user['full_name'],
            'last_login' => $user['last_login']
        ],
        'token' => $token
    ]);
}

/**
 * Verify JWT token
 */
function handleVerify($request, $config) {
    $token = $request->getBearerToken();
    
    if (!$token) {
        Response::unauthorized('No token provided');
    }
    
    try {
        $payload = JWT::decode($token, $config['jwt']['secret']);
        Response::success('Token is valid', ['payload' => $payload]);
    } catch (Exception $e) {
        Response::unauthorized('Invalid or expired token');
    }
}

/**
 * Refresh JWT token
 */
function handleRefresh($request, $db, $config) {
    $token = $request->getBearerToken();
    
    if (!$token) {
        Response::unauthorized('No token provided');
    }
    
    try {
        $payload = JWT::decode($token, $config['jwt']['secret']);
        
        // Verify user still exists and is active
        $user = $db->fetchOne(
            "SELECT id, username, email, is_active FROM users WHERE id = ?",
            [$payload['user_id']]
        );
        
        if (!$user || !$user['is_active']) {
            Response::unauthorized('User not found or disabled');
        }
        
        // Generate new token
        $newToken = JWT::encode(
            [
                'user_id' => $user['id'],
                'username' => $user['username'],
                'email' => $user['email']
            ],
            $config['jwt']['secret'],
            $config['jwt']['expiration']
        );
        
        Response::success('Token refreshed', ['token' => $newToken]);
        
    } catch (Exception $e) {
        Response::unauthorized('Invalid or expired token');
    }
}

/**
 * Get user information
 */
function handleGetUser($request, $db, $config) {
    $token = $request->getBearerToken();
    
    if (!$token) {
        Response::unauthorized('No token provided');
    }
    
    try {
        $payload = JWT::decode($token, $config['jwt']['secret']);
        
        $user = $db->fetchOne(
            "SELECT id, username, email, full_name, created_at, last_login, is_active FROM users WHERE id = ?",
            [$payload['user_id']]
        );
        
        if (!$user) {
            Response::notFound('User not found');
        }
        
        Response::success('User information', ['user' => $user]);
        
    } catch (Exception $e) {
        Response::unauthorized('Invalid or expired token');
    }
}
