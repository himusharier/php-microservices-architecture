# 📋 Project Overview

## Complete File Structure

```
microservice-framework/
│
├── 📄 .htaccess                      # Main Apache routing configuration
├── 📄 index.php                      # Homepage / Welcome page
├── 📄 config.php                     # Global configuration
│
├── 📚 Documentation
│   ├── README.md                     # Full documentation
│   ├── QUICKSTART.md                 # Quick start guide
│   ├── API_EXAMPLES.md              # API testing examples
│   ├── ADDING_SERVICES.md           # How to add new services
│   └── database.sql                  # Database schema reference
│
├── 📁 app/
│   │
│   ├── 📁 gateway/
│   │   └── index.php                # API Gateway - Routes all API requests
│   │
│   ├── 📁 services/                 # Microservices
│   │   │
│   │   ├── 📁 auth-service/         # Authentication & Authorization
│   │   │   └── index.php            # - User registration
│   │   │                            # - Login/logout
│   │   │                            # - JWT token management
│   │   │                            # - Token verification
│   │   │
│   │   ├── 📁 configuration-service/  # Configuration Management
│   │   │   └── index.php              # - Get/set configurations
│   │   │                              # - Update settings
│   │   │                              # - Centralized config storage
│   │   │
│   │   └── 📁 database-service/       # Database Operations
│   │       └── index.php              # - Health checks
│   │                                  # - Table management
│   │                                  # - Query execution
│   │
│   ├── 📁 clients/                   # Frontend Applications
│   │   │
│   │   ├── 📁 public-frontend/       # Public Website
│   │   │   └── index.php             # - Landing page
│   │   │                             # - Feature showcase
│   │   │                             # - API testing interface
│   │   │
│   │   └── 📁 user-frontend/         # User Dashboard
│   │       └── index.php             # - User registration form
│   │                                 # - Login interface
│   │                                 # - Protected dashboard
│   │                                 # - Profile management
│   │
│   └── 📁 shared/                    # Shared Utilities
│       ├── Request.php               # HTTP request parsing
│       ├── Response.php              # JSON response formatting
│       ├── Router.php                # URL routing with parameters
│       ├── Database.php              # Database connection (Singleton)
│       └── JWT.php                   # JWT token generation/validation
│
└── 📁 assets/                        # Static files (images, css, js)
```

## Architecture Diagram

```
┌─────────────────────────────────────────────────────────────┐
│                         CLIENTS                              │
├──────────────────┬─────────────────┬────────────────────────┤
│  Public Frontend │  User Frontend  │   External Apps/APIs   │
│  (Landing Page)  │  (Dashboard)    │   (Mobile, etc)        │
└────────┬─────────┴────────┬────────┴────────────┬───────────┘
         │                  │                     │
         └──────────────────┼─────────────────────┘
                            │
                ┌───────────▼───────────┐
                │    .htaccess Router   │
                │  (Apache Routing)     │
                └───────────┬───────────┘
                            │
         ┌──────────────────┼──────────────────┐
         │                  │                  │
    /public/           /user/            /api/*
         │                  │                  │
         ▼                  ▼                  ▼
┌─────────────────┐ ┌─────────────────┐ ┌─────────────────┐
│ Public Frontend │ │  User Frontend  │ │   API Gateway   │
│   (Static UI)   │ │   (Static UI)   │ │  (Route Logic)  │
└─────────────────┘ └─────────────────┘ └────────┬────────┘
                                                  │
                     ┌────────────────────────────┼────────────┐
                     │                            │            │
                     ▼                            ▼            ▼
            ┌─────────────────┐         ┌─────────────────┐ ┌──────────────┐
            │  Auth Service   │         │ Config Service  │ │  DB Service  │
            │  ┌───────────┐  │         │  ┌───────────┐  │ │ ┌──────────┐ │
            │  │Controller │  │         │  │Controller │  │ │ │Controller│ │
            │  └─────┬─────┘  │         │  └─────┬─────┘  │ │ └────┬─────┘ │
            └────────┼────────┘         └────────┼────────┘ └──────┼───────┘
                     │                           │                 │
                     └───────────────┬───────────┴─────────────────┘
                                     │
                         ┌───────────▼───────────┐
                         │   Shared Utilities    │
                         │ Request | Response    │
                         │ Router  | Database    │
                         │    JWT  Handler       │
                         └───────────┬───────────┘
                                     │
                         ┌───────────▼───────────┐
                         │   MySQL Database      │
                         │  ┌─────────────────┐  │
                         │  │ users           │  │
                         │  │ configurations  │  │
                         │  │ (+ your tables) │  │
                         │  └─────────────────┘  │
                         └───────────────────────┘
```

## Request Flow

### Example: User Registration

```
1. User fills form on /user/ frontend
   ↓
2. JavaScript sends POST to /api/auth/register
   ↓
3. .htaccess routes to /app/gateway/index.php
   ↓
4. Gateway router matches route /auth/register
   ↓
5. Gateway forwards to auth-service with action='register'
   ↓
6. Auth Service:
   - Validates input
   - Checks for duplicates
   - Hashes password
   - Inserts into database
   - Generates JWT token
   ↓
7. Response sent back through Gateway
   ↓
8. Frontend receives JSON response
   ↓
9. User logged in, token stored, dashboard shown
```

## Database Schema

### Users Table (Auth Service)
```sql
users
├── id (PK, AUTO_INCREMENT)
├── username (UNIQUE)
├── email (UNIQUE)
├── password (BCRYPT HASH)
├── full_name
├── created_at
├── updated_at
├── last_login
└── is_active
```

### Configurations Table (Config Service)
```sql
configurations
├── id (PK, AUTO_INCREMENT)
├── config_key (UNIQUE)
├── config_value
├── description
├── is_encrypted
├── created_at
└── updated_at
```

## API Endpoints Summary

### Gateway & Health
- `GET  /api/health` - API health check
- `GET  /api/services` - List all services

### Authentication
- `POST /api/auth/register` - Create account
- `POST /api/auth/login` - Get JWT token
- `POST /api/auth/verify` - Verify token
- `POST /api/auth/refresh` - Refresh token
- `GET  /api/auth/user` - Get user info (🔒 protected)

### Configuration
- `GET  /api/config` - Get all configs
- `GET  /api/config/:key` - Get specific config
- `POST /api/config` - Create config
- `PUT  /api/config/:key` - Update config

### Database
- `GET  /api/db/health` - Database health
- `GET  /api/db/tables` - List tables
- `POST /api/db/query` - Execute SELECT query

## Technology Stack

| Layer | Technology |
|-------|-----------|
| **Language** | PHP 8.2 |
| **Web Server** | Apache 2.4 |
| **Database** | MySQL 5.7+ |
| **Authentication** | JWT (Custom Implementation) |
| **Routing** | Custom Router with Regex |
| **Architecture** | Microservices |
| **API Style** | RESTful |
| **Response Format** | JSON |

## Key Features

### ✅ Security
- Password hashing (bcrypt)
- JWT token authentication
- SQL injection prevention (prepared statements)
- Input validation
- CORS configuration

### ✅ Architecture
- Clean separation of concerns
- Independent microservices
- Single entry point (API Gateway)
- Shared utility layer
- Database abstraction

### ✅ Development
- No external frameworks required
- Pure PHP OOP
- Easy to understand and modify
- Well-documented code
- Example implementations

### ✅ Scalability
- Service-oriented architecture
- Each service can scale independently
- Stateless authentication (JWT)
- Database connection pooling

## URLs Quick Reference

| Purpose | URL |
|---------|-----|
| Homepage | `http://localhost/microservice-framework/` |
| Public Site | `http://localhost/microservice-framework/public/` |
| User Dashboard | `http://localhost/microservice-framework/user/` |
| API Gateway | `http://localhost/microservice-framework/api/` |
| API Health | `http://localhost/microservice-framework/api/health` |
| API Docs | `http://localhost/microservice-framework/api/services` |

## Next Steps

1. ✅ **You're running!** - Everything is set up
2. 📖 **Read QUICKSTART.md** - 5-minute getting started guide
3. 🧪 **Test the APIs** - Use API_EXAMPLES.md
4. 🔨 **Add your service** - Follow ADDING_SERVICES.md
5. 🚀 **Deploy** - Move to production server when ready

## Support & Resources

- **Full Documentation**: README.md
- **Quick Start**: QUICKSTART.md
- **API Examples**: API_EXAMPLES.md
- **Add Services**: ADDING_SERVICES.md
- **Database Schema**: database.sql

---

**Status**: ✅ Fully Operational

Built with ❤️ using Raw PHP, Apache, and MySQL
