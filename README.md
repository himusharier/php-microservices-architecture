# Microservice Framework - Raw PHP

A complete microservice architecture built with raw PHP 8.2, Apache, and MySQL. No frameworks required!

## 🚀 Features

- **API Gateway** - Single entry point routing requests to microservices
- **Auth Service** - JWT-based authentication and authorization
- **Configuration Service** - Centralized configuration management
- **Database Service** - Database operations and health monitoring
- **Public Frontend** - Landing page and public interface
- **User Frontend** - User dashboard with authentication
- **Shared Utilities** - Reusable components across services

## 📁 Project Structure

```
microservice-framework/
├── .htaccess                  # Main routing configuration
├── index.php                  # Homepage
├── config.php                 # Global configuration
├── app/
│   ├── gateway/
│   │   └── index.php         # API Gateway entry point
│   ├── services/
│   │   ├── auth-service/     # Authentication microservice
│   │   ├── configuration-service/  # Config management
│   │   └── database-service/ # Database operations
│   ├── clients/
│   │   ├── public-frontend/  # Public website
│   │   └── user-frontend/    # User dashboard
│   └── shared/               # Shared utilities
│       ├── Request.php       # HTTP request handler
│       ├── Response.php      # HTTP response handler
│       ├── Router.php        # URL routing
│       ├── Database.php      # Database connection
│       └── JWT.php           # JWT token management
└── assets/                   # Static assets
```

## 🛠️ Installation

### Prerequisites

- PHP 8.2 or higher
- Apache with mod_rewrite enabled
- MySQL 5.7 or higher
- XAMPP or similar local server

### Setup Steps

1. **Place project in XAMPP htdocs:**
   ```
   c:\xampp\htdocs\microservice-framework\
   ```

2. **Configure Apache:**
   - Ensure `mod_rewrite` is enabled in Apache
   - The `.htaccess` file is already configured

3. **Configure Database:**
   - Update database credentials in `config.php`:
   ```php
   'database' => [
       'host' => 'localhost',
       'port' => '3306',
       'database' => 'microservice_db',
       'username' => 'root',
       'password' => '',
   ],
   ```

4. **Start Apache and MySQL:**
   - Start XAMPP Control Panel
   - Start Apache and MySQL services

5. **Access the application:**
   ```
   http://localhost/microservice-framework/
   ```

The database and tables will be created automatically on first access.

## 📡 API Endpoints

### Health & Info
- `GET /api/health` - Check API Gateway health
- `GET /api/services` - List all available services

### Authentication Service
- `POST /api/auth/register` - Register new user
  ```json
  {
    "username": "john_doe",
    "email": "john@example.com",
    "password": "password123",
    "full_name": "John Doe"
  }
  ```

- `POST /api/auth/login` - Login user
  ```json
  {
    "login": "john_doe",
    "password": "password123"
  }
  ```

- `POST /api/auth/verify` - Verify JWT token (requires Bearer token)
- `POST /api/auth/refresh` - Refresh JWT token (requires Bearer token)
- `GET /api/auth/user` - Get user info (requires Bearer token)

### Configuration Service
- `GET /api/config` - Get all configurations
- `GET /api/config/:key` - Get specific configuration
- `POST /api/config` - Create new configuration
  ```json
  {
    "key": "app.feature_flag",
    "value": "true",
    "description": "Enable new feature"
  }
  ```
- `PUT /api/config/:key` - Update configuration

### Database Service
- `GET /api/db/health` - Check database health
- `GET /api/db/tables` - List all database tables
- `POST /api/db/query` - Execute SELECT query
  ```json
  {
    "sql": "SELECT * FROM users LIMIT 10",
    "params": []
  }
  ```

## 🔐 Authentication

All protected endpoints require a JWT token in the Authorization header:

```
Authorization: Bearer <your-jwt-token>
```

Get a token by registering or logging in through the Auth Service.

## 🌐 Frontend Access

- **Homepage:** `http://localhost/microservice-framework/`
- **Public Frontend:** `http://localhost/microservice-framework/public/`
- **User Dashboard:** `http://localhost/microservice-framework/user/`

## 🧪 Testing the API

### Using cURL:

**Health Check:**
```bash
curl http://localhost/microservice-framework/api/health
```

**Register User:**
```bash
curl -X POST http://localhost/microservice-framework/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{"username":"testuser","email":"test@example.com","password":"password123"}'
```

**Login:**
```bash
curl -X POST http://localhost/microservice-framework/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"login":"testuser","password":"password123"}'
```

### Using the Web Interface:

1. Visit `http://localhost/microservice-framework/public/`
2. Click "Try Our API Endpoints" to test endpoints
3. For authenticated access, go to User Dashboard and register/login

## 🏗️ Architecture

### Microservice Pattern

Each service is independent and can be:
- Developed separately
- Deployed independently
- Scaled independently
- Maintained by different teams

### API Gateway Pattern

- Single entry point for all client requests
- Routes requests to appropriate microservices
- Handles authentication and authorization
- Provides service discovery

### Shared Utilities

Common functionality across services:
- **Request/Response** - HTTP handling
- **Router** - URL routing with parameters
- **Database** - Singleton database connection
- **JWT** - Token generation and validation

## 🔧 Configuration

Edit `config.php` to customize:

- Application settings
- Database connection
- JWT secret and expiration
- Service enablement
- CORS settings

## 📊 Database Schema

### Users Table (Auth Service)
```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    is_active BOOLEAN DEFAULT TRUE
);
```

### Configurations Table (Config Service)
```sql
CREATE TABLE configurations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    config_key VARCHAR(100) UNIQUE NOT NULL,
    config_value TEXT,
    description VARCHAR(255),
    is_encrypted BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

## 🛡️ Security Features

- Password hashing with bcrypt
- JWT token-based authentication
- SQL injection prevention with prepared statements
- CORS configuration
- Input validation

## 🚦 Status

All services are operational:
- ✅ API Gateway
- ✅ Auth Service
- ✅ Configuration Service
- ✅ Database Service
- ✅ Public Frontend
- ✅ User Frontend

## 📝 License

This is an open-source project for educational and development purposes.

## 🤝 Contributing

Feel free to extend this framework with additional microservices:
1. Create a new folder in `app/services/`
2. Add routing in the API Gateway
3. Follow the existing service patterns

## 📞 Support

For issues or questions, check the code comments or modify the configuration to fit your needs.
