<?php
/**
 * Shared Utilities - HTTP Request Handler
 */

class Request {
    private $method;
    private $uri;
    private $headers;
    private $body;
    private $queryParams;
    
    public function __construct() {
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $this->headers = $this->getHeaders();
        $this->queryParams = $_GET;
        $this->body = $this->parseBody();
    }
    
    private function getHeaders() {
        $headers = [];
        foreach ($_SERVER as $key => $value) {
            if (strpos($key, 'HTTP_') === 0) {
                $header = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));
                $headers[$header] = $value;
            }
        }
        return $headers;
    }
    
    private function parseBody() {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        
        if (strpos($contentType, 'application/json') !== false) {
            return json_decode(file_get_contents('php://input'), true) ?? [];
        }
        
        return $_POST;
    }
    
    public function getMethod() {
        return $this->method;
    }
    
    public function getUri() {
        return $this->uri;
    }
    
    public function getHeader($name) {
        return $this->headers[$name] ?? null;
    }
    
    public function getBody() {
        return $this->body;
    }
    
    public function getQuery($key = null) {
        if ($key === null) {
            return $this->queryParams;
        }
        return $this->queryParams[$key] ?? null;
    }
    
    public function getBearerToken() {
        $authHeader = $this->getHeader('Authorization');
        if ($authHeader && preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            return $matches[1];
        }
        return null;
    }
}
