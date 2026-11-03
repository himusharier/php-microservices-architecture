<?php
/**
 * Shared Utilities - HTTP Response Handler
 */

class Response {
    public static function json($data, $statusCode = 200, $headers = []) {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        
        foreach ($headers as $key => $value) {
            header("$key: $value");
        }
        
        echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    public static function success($message, $data = null, $statusCode = 200) {
        $response = [
            'success' => true,
            'message' => $message,
            'timestamp' => date('Y-m-d H:i:s'),
        ];
        
        if ($data !== null) {
            $response['data'] = $data;
        }
        
        self::json($response, $statusCode);
    }
    
    public static function error($message, $statusCode = 400, $errors = null) {
        $response = [
            'success' => false,
            'error' => $message,
            'timestamp' => date('Y-m-d H:i:s'),
        ];
        
        if ($errors !== null) {
            $response['errors'] = $errors;
        }
        
        self::json($response, $statusCode);
    }
    
    public static function notFound($message = 'Resource not found') {
        self::error($message, 404);
    }
    
    public static function unauthorized($message = 'Unauthorized access') {
        self::error($message, 401);
    }
    
    public static function forbidden($message = 'Access forbidden') {
        self::error($message, 403);
    }
    
    public static function serverError($message = 'Internal server error') {
        self::error($message, 500);
    }
}
