<?php
/**
 * Shared Utilities - Router
 */

class Router {
    private $routes = [];
    private $basePath = '';
    
    public function __construct($basePath = '') {
        $this->basePath = rtrim($basePath, '/');
    }
    
    public function get($path, $callback) {
        $this->addRoute('GET', $path, $callback);
    }
    
    public function post($path, $callback) {
        $this->addRoute('POST', $path, $callback);
    }
    
    public function put($path, $callback) {
        $this->addRoute('PUT', $path, $callback);
    }
    
    public function delete($path, $callback) {
        $this->addRoute('DELETE', $path, $callback);
    }
    
    private function addRoute($method, $path, $callback) {
        $path = $this->basePath . '/' . ltrim($path, '/');
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'callback' => $callback,
        ];
    }
    
    public function dispatch($request) {
        $requestMethod = $request->getMethod();
        $requestUri = $request->getUri();
        
        // Remove query string
        $requestUri = strtok($requestUri, '?');
        
        // Strip the base directory from URI (e.g., /microservice-framework/api/health -> /api/health)
        $scriptName = $_SERVER['SCRIPT_NAME']; // e.g., /microservice-framework/app/gateway/index.php
        $baseDir = dirname(dirname(dirname($scriptName))); // e.g., /microservice-framework
        if ($baseDir !== '/' && strpos($requestUri, $baseDir) === 0) {
            $requestUri = substr($requestUri, strlen($baseDir));
        }
        
        foreach ($this->routes as $route) {
            if ($route['method'] !== $requestMethod) {
                continue;
            }
            
            $pattern = $this->convertPathToRegex($route['path']);
            
            if (preg_match($pattern, $requestUri, $matches)) {
                array_shift($matches); // Remove full match
                return call_user_func_array($route['callback'], array_merge([$request], $matches));
            }
        }
        
        Response::notFound('Route not found');
    }
    
    private function convertPathToRegex($path) {
        // Convert :param to named capture groups
        $pattern = preg_replace('/\/:([^\/]+)/', '/(?P<$1>[^/]+)', $path);
        return '#^' . $pattern . '$#';
    }
}
