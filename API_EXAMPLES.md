# API Testing Examples

## Using cURL

### 1. Check API Health
```bash
curl http://localhost/microservice-framework/api/health
```

### 2. List Available Services
```bash
curl http://localhost/microservice-framework/api/services
```

### 3. Register New User
```bash
curl -X POST http://localhost/microservice-framework/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "username": "john_doe",
    "email": "john@example.com",
    "password": "password123",
    "full_name": "John Doe"
  }'
```

### 4. Login
```bash
curl -X POST http://localhost/microservice-framework/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "login": "john_doe",
    "password": "password123"
  }'
```

Save the token from the response!

### 5. Verify Token
```bash
curl -X POST http://localhost/microservice-framework/api/auth/verify \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

### 6. Get User Info
```bash
curl http://localhost/microservice-framework/api/auth/user \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

### 7. Get All Configurations
```bash
curl http://localhost/microservice-framework/api/config
```

### 8. Get Specific Configuration
```bash
curl http://localhost/microservice-framework/api/config/app.name
```

### 9. Create Configuration
```bash
curl -X POST http://localhost/microservice-framework/api/config \
  -H "Content-Type: application/json" \
  -d '{
    "key": "feature.new_dashboard",
    "value": "enabled",
    "description": "Enable new dashboard feature"
  }'
```

### 10. Update Configuration
```bash
curl -X PUT http://localhost/microservice-framework/api/config/feature.new_dashboard \
  -H "Content-Type: application/json" \
  -d '{
    "value": "disabled",
    "description": "Disable new dashboard feature"
  }'
```

### 11. Database Health Check
```bash
curl http://localhost/microservice-framework/api/db/health
```

### 12. List Database Tables
```bash
curl http://localhost/microservice-framework/api/db/tables
```

### 13. Execute Database Query
```bash
curl -X POST http://localhost/microservice-framework/api/db/query \
  -H "Content-Type: application/json" \
  -d '{
    "sql": "SELECT * FROM users LIMIT 5"
  }'
```

## Using Postman

Import these as a Postman collection:

1. Create a new collection called "Microservice Framework"
2. Add environment variable: `base_url = http://localhost/microservice-framework`
3. Add environment variable: `token` (will be set after login)

**Request Examples:**

### Register User
- Method: POST
- URL: `{{base_url}}/api/auth/register`
- Headers: `Content-Type: application/json`
- Body (JSON):
```json
{
  "username": "testuser",
  "email": "test@example.com",
  "password": "password123",
  "full_name": "Test User"
}
```
- Tests tab (to save token):
```javascript
pm.environment.set("token", pm.response.json().data.token);
```

### Login
- Method: POST
- URL: `{{base_url}}/api/auth/login`
- Headers: `Content-Type: application/json`
- Body (JSON):
```json
{
  "login": "testuser",
  "password": "password123"
}
```
- Tests tab:
```javascript
pm.environment.set("token", pm.response.json().data.token);
```

### Get User Info (Protected)
- Method: GET
- URL: `{{base_url}}/api/auth/user`
- Headers: `Authorization: Bearer {{token}}`

## Using JavaScript (Fetch API)

### Register User
```javascript
fetch('http://localhost/microservice-framework/api/auth/register', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json'
  },
  body: JSON.stringify({
    username: 'testuser',
    email: 'test@example.com',
    password: 'password123',
    full_name: 'Test User'
  })
})
.then(response => response.json())
.then(data => {
  console.log('Token:', data.data.token);
  localStorage.setItem('authToken', data.data.token);
});
```

### Get Protected Data
```javascript
const token = localStorage.getItem('authToken');

fetch('http://localhost/microservice-framework/api/auth/user', {
  headers: {
    'Authorization': `Bearer ${token}`
  }
})
.then(response => response.json())
.then(data => console.log('User:', data.data.user));
```

## Expected Responses

### Successful Response
```json
{
  "success": true,
  "message": "Operation successful",
  "timestamp": "2026-03-01 12:00:00",
  "data": {
    // Response data here
  }
}
```

### Error Response
```json
{
  "success": false,
  "error": "Error message",
  "timestamp": "2026-03-01 12:00:00",
  "errors": {
    // Optional error details
  }
}
```

## Status Codes

- `200` - Success
- `201` - Created
- `400` - Bad Request
- `401` - Unauthorized
- `403` - Forbidden
- `404` - Not Found
- `409` - Conflict (duplicate)
- `500` - Server Error
