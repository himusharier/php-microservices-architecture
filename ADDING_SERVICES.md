# Adding New Microservices

This guide shows you how to add new microservices to the framework.

## Quick Template

### 1. Create Service Folder
Create a new folder in `/app/services/`:
```
app/services/your-service-name/
```

### 2. Create Service Entry Point
Create `index.php` in your service folder:

```php
<?php
/**
 * Your Service Name - Description
 */

// Load shared utilities
require_once __DIR__ . '/../../shared/Request.php';
require_once __DIR__ . '/../../shared/Response.php';
require_once __DIR__ . '/../../shared/Database.php';
require_once __DIR__ . '/../../shared/JWT.php';

// Load configuration
$config = require __DIR__ . '/../../../config.php';

// Initialize database (if needed)
$db = Database::getInstance();

// Create your tables (if needed)
$db->execute("
    CREATE TABLE IF NOT EXISTS your_table (
        id INT AUTO_INCREMENT PRIMARY KEY,
        -- your columns here
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )
");

// Get action from gateway
$action = $_ENV['SERVICE_ACTION'] ?? 'unknown';
$params = json_decode($_ENV['SERVICE_PARAMS'] ?? '{}', true);

// Get request object
$request = new Request();

// Handle actions
switch ($action) {
    case 'list':
        handleList($request, $db);
        break;
        
    case 'get':
        handleGet($request, $db, $params);
        break;
        
    case 'create':
        handleCreate($request, $db);
        break;
        
    case 'update':
        handleUpdate($request, $db, $params);
        break;
        
    case 'delete':
        handleDelete($request, $db, $params);
        break;
        
    default:
        Response::error('Invalid action', 400);
}

/**
 * List all items
 */
function handleList($request, $db) {
    $items = $db->fetchAll("SELECT * FROM your_table");
    Response::success('Items retrieved', [
        'items' => $items,
        'count' => count($items)
    ]);
}

/**
 * Get single item
 */
function handleGet($request, $db, $params) {
    $id = $params['id'] ?? null;
    
    if (!$id) {
        Response::error('ID is required', 400);
    }
    
    $item = $db->fetchOne(
        "SELECT * FROM your_table WHERE id = ?",
        [$id]
    );
    
    if (!$item) {
        Response::notFound('Item not found');
    }
    
    Response::success('Item retrieved', ['item' => $item]);
}

/**
 * Create new item
 */
function handleCreate($request, $db) {
    $body = $request->getBody();
    
    // Validate input
    if (empty($body['name'])) {
        Response::error('Name is required', 400);
    }
    
    // Insert item
    $db->execute(
        "INSERT INTO your_table (name) VALUES (?)",
        [$body['name']]
    );
    
    $id = $db->lastInsertId();
    
    Response::success('Item created', [
        'id' => $id,
        'name' => $body['name']
    ], 201);
}

/**
 * Update item
 */
function handleUpdate($request, $db, $params) {
    $id = $params['id'] ?? null;
    $body = $request->getBody();
    
    if (!$id) {
        Response::error('ID is required', 400);
    }
    
    // Check if exists
    $existing = $db->fetchOne(
        "SELECT id FROM your_table WHERE id = ?",
        [$id]
    );
    
    if (!$existing) {
        Response::notFound('Item not found');
    }
    
    // Update item
    $db->execute(
        "UPDATE your_table SET name = ? WHERE id = ?",
        [$body['name'], $id]
    );
    
    Response::success('Item updated', ['id' => $id]);
}

/**
 * Delete item
 */
function handleDelete($request, $db, $params) {
    $id = $params['id'] ?? null;
    
    if (!$id) {
        Response::error('ID is required', 400);
    }
    
    $deleted = $db->execute(
        "DELETE FROM your_table WHERE id = ?",
        [$id]
    );
    
    if ($deleted === 0) {
        Response::notFound('Item not found');
    }
    
    Response::success('Item deleted', ['id' => $id]);
}
```

### 3. Add Routes to Gateway
Edit `/app/gateway/index.php` and add your routes:

```php
// Route to Your Service
$router->get('/yourservice', function($request) {
    return forward($request, 'your-service-name', 'list');
});

$router->get('/yourservice/:id', function($request, $id) {
    return forward($request, 'your-service-name', 'get', ['id' => $id]);
});

$router->post('/yourservice', function($request) {
    return forward($request, 'your-service-name', 'create');
});

$router->put('/yourservice/:id', function($request, $id) {
    return forward($request, 'your-service-name', 'update', ['id' => $id]);
});

$router->delete('/yourservice/:id', function($request, $id) {
    return forward($request, 'your-service-name', 'delete', ['id' => $id]);
});
```

### 4. Test Your Service

```bash
# List all items
curl http://localhost/microservice-framework/api/yourservice

# Get single item
curl http://localhost/microservice-framework/api/yourservice/1

# Create item
curl -X POST http://localhost/microservice-framework/api/yourservice \
  -H "Content-Type: application/json" \
  -d '{"name":"Test Item"}'

# Update item
curl -X PUT http://localhost/microservice-framework/api/yourservice/1 \
  -H "Content-Type: application/json" \
  -d '{"name":"Updated Item"}'

# Delete item
curl -X DELETE http://localhost/microservice-framework/api/yourservice/1
```

## Example: Product Service

Here's a complete example of a Product microservice:

### File: `/app/services/product-service/index.php`

```php
<?php
/**
 * Product Service - Manage Products
 */

require_once __DIR__ . '/../../shared/Request.php';
require_once __DIR__ . '/../../shared/Response.php';
require_once __DIR__ . '/../../shared/Database.php';

$config = require __DIR__ . '/../../../config.php';
$db = Database::getInstance();

// Create products table
$db->execute("
    CREATE TABLE IF NOT EXISTS products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(200) NOT NULL,
        description TEXT,
        price DECIMAL(10,2) NOT NULL,
        stock INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )
");

$action = $_ENV['SERVICE_ACTION'] ?? 'unknown';
$params = json_decode($_ENV['SERVICE_PARAMS'] ?? '{}', true);
$request = new Request();

switch ($action) {
    case 'list':
        $products = $db->fetchAll("SELECT * FROM products");
        Response::success('Products retrieved', [
            'products' => $products,
            'count' => count($products)
        ]);
        break;
        
    case 'get':
        $id = $params['id'];
        $product = $db->fetchOne("SELECT * FROM products WHERE id = ?", [$id]);
        if (!$product) Response::notFound('Product not found');
        Response::success('Product retrieved', ['product' => $product]);
        break;
        
    case 'create':
        $body = $request->getBody();
        if (empty($body['name']) || empty($body['price'])) {
            Response::error('Name and price are required', 400);
        }
        
        $db->execute(
            "INSERT INTO products (name, description, price, stock) VALUES (?, ?, ?, ?)",
            [$body['name'], $body['description'] ?? '', $body['price'], $body['stock'] ?? 0]
        );
        
        Response::success('Product created', ['id' => $db->lastInsertId()], 201);
        break;
        
    default:
        Response::error('Invalid action', 400);
}
```

### Add to Gateway:

```php
// Product Service Routes
$router->get('/products', function($request) {
    return forward($request, 'product-service', 'list');
});

$router->get('/products/:id', function($request, $id) {
    return forward($request, 'product-service', 'get', ['id' => $id]);
});

$router->post('/products', function($request) {
    return forward($request, 'product-service', 'create');
});
```

### Test:

```bash
# Create product
curl -X POST http://localhost/microservice-framework/api/products \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Laptop",
    "description": "High-end gaming laptop",
    "price": 1299.99,
    "stock": 10
  }'

# List products
curl http://localhost/microservice-framework/api/products
```

## Best Practices

1. **Keep services independent** - Each service should manage its own data
2. **Use shared utilities** - Leverage Request, Response, Database, JWT classes
3. **Validate input** - Always validate and sanitize user input
4. **Handle errors gracefully** - Use Response::error() with appropriate status codes
5. **Document your API** - Add service info to `/api/services` endpoint
6. **Use transactions** - For complex database operations
7. **Add authentication** - Use JWT tokens for protected endpoints

## Advanced: Protected Endpoints

To require authentication:

```php
function handleProtectedAction($request, $db) {
    // Verify JWT token
    $token = $request->getBearerToken();
    if (!$token) {
        Response::unauthorized('No token provided');
    }
    
    $config = require __DIR__ . '/../../../config.php';
    
    try {
        $payload = JWT::decode($token, $config['jwt']['secret']);
        $userId = $payload['user_id'];
        
        // Your protected logic here
        Response::success('Protected data', ['user_id' => $userId]);
        
    } catch (Exception $e) {
        Response::unauthorized('Invalid token');
    }
}
```

## Need Help?

- Check existing services in `/app/services/` for examples
- Use the shared utilities in `/app/shared/`
- Follow RESTful API conventions
- Test thoroughly with cURL or Postman

Happy coding! 🚀
