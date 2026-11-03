<?php
/**
 * Database Service - Database Operations and Utilities
 */

// Load shared utilities
require_once __DIR__ . '/../../shared/Request.php';
require_once __DIR__ . '/../../shared/Response.php';
require_once __DIR__ . '/../../shared/Database.php';

// Load configuration
$config = require __DIR__ . '/../../../config.php';

// Initialize database
$db = Database::getInstance();

// Get action from gateway
$action = $_ENV['SERVICE_ACTION'] ?? 'unknown';
$params = json_decode($_ENV['SERVICE_PARAMS'] ?? '{}', true);

// Get request object
$request = new Request();

// Handle actions
switch ($action) {
    case 'health':
        handleHealth($db, $config);
        break;
        
    case 'tables':
        handleTables($db);
        break;
        
    case 'query':
        handleQuery($request, $db);
        break;
        
    default:
        Response::error('Invalid action', 400);
}

/**
 * Database health check
 */
function handleHealth($db, $config) {
    try {
        $connection = $db->getConnection();
        $version = $connection->query('SELECT VERSION()')->fetchColumn();
        
        // Get database size
        $dbName = $config['database']['database'];
        $sizeQuery = $connection->query("
            SELECT 
                ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS size_mb
            FROM information_schema.TABLES 
            WHERE table_schema = '$dbName'
        ");
        $size = $sizeQuery->fetchColumn();
        
        // Get table count
        $tableCount = $connection->query("
            SELECT COUNT(*) 
            FROM information_schema.TABLES 
            WHERE table_schema = '$dbName'
        ")->fetchColumn();
        
        Response::success('Database is healthy', [
            'status' => 'connected',
            'database' => $dbName,
            'version' => $version,
            'size_mb' => $size ?? 0,
            'table_count' => $tableCount,
            'host' => $config['database']['host'],
            'port' => $config['database']['port']
        ]);
        
    } catch (Exception $e) {
        Response::error('Database connection failed', 500, [
            'message' => $e->getMessage()
        ]);
    }
}

/**
 * Get all tables in database
 */
function handleTables($db) {
    try {
        $tables = $db->fetchAll("SHOW TABLES");
        
        $tableList = [];
        foreach ($tables as $table) {
            $tableName = array_values($table)[0];
            
            // Get row count
            $countResult = $db->fetchOne("SELECT COUNT(*) as count FROM `$tableName`");
            $rowCount = $countResult['count'];
            
            // Get table structure
            $columns = $db->fetchAll("DESCRIBE `$tableName`");
            
            $tableList[] = [
                'name' => $tableName,
                'row_count' => $rowCount,
                'columns' => array_map(function($col) {
                    return [
                        'name' => $col['Field'],
                        'type' => $col['Type'],
                        'null' => $col['Null'],
                        'key' => $col['Key'],
                        'default' => $col['Default']
                    ];
                }, $columns)
            ];
        }
        
        Response::success('Tables retrieved', [
            'tables' => $tableList,
            'count' => count($tableList)
        ]);
        
    } catch (Exception $e) {
        Response::error('Failed to retrieve tables', 500, [
            'message' => $e->getMessage()
        ]);
    }
}

/**
 * Execute a custom query (with safety restrictions)
 */
function handleQuery($request, $db) {
    $body = $request->getBody();
    
    if (empty($body['sql'])) {
        Response::error('SQL query is required', 400);
    }
    
    $sql = trim($body['sql']);
    $params = $body['params'] ?? [];
    
    // Safety check: only allow SELECT queries for now
    if (!preg_match('/^\s*SELECT/i', $sql)) {
        Response::error('Only SELECT queries are allowed', 403);
    }
    
    try {
        $results = $db->fetchAll($sql, $params);
        
        Response::success('Query executed successfully', [
            'results' => $results,
            'count' => count($results)
        ]);
        
    } catch (Exception $e) {
        Response::error('Query execution failed', 500, [
            'message' => $e->getMessage()
        ]);
    }
}
