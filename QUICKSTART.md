# 🚀 Quick Start Guide

## 1. Prerequisites Check

✅ PHP 8.2+ installed  
✅ Apache server running  
✅ MySQL server running  
✅ mod_rewrite enabled in Apache

## 2. Installation (5 minutes)

### Step 1: Verify Location
Make sure your project is in:
```
c:\xampp\htdocs\microservice-framework\
```

### Step 2: Start Services
1. Open XAMPP Control Panel
2. Start **Apache**
3. Start **MySQL**

### Step 3: Configure Database (Optional)
Edit `config.php` if your MySQL credentials are different:
```php
'database' => [
    'username' => 'root',      // Change if needed
    'password' => '',          // Change if needed
],
```

### Step 4: Access Application
Open your browser and go to:
```
http://localhost/microservice-framework/
```

**That's it!** The database and tables will be created automatically.

## 3. First Test (2 minutes)

### Test the API Gateway
Visit: `http://localhost/microservice-framework/api/health`

You should see:
```json
{
  "success": true,
  "message": "API Gateway is running",
  "data": {
    "version": "1.0.0",
    "services": { ... }
  }
}
```

### Test the Public Frontend
Visit: `http://localhost/microservice-framework/public/`

You should see a beautiful landing page with API test buttons.

### Test the User Dashboard
Visit: `http://localhost/microservice-framework/user/`

Register a new account and login!

## 4. Create Your First User

### Option A: Using the Web Interface
1. Go to `http://localhost/microservice-framework/user/`
2. Click "Register" tab
3. Fill in the form:
   - Username: `admin`
   - Email: `admin@test.com`
   - Password: `admin123`
4. Click "Register"

### Option B: Using cURL
```bash
curl -X POST http://localhost/microservice-framework/api/auth/register \
  -H "Content-Type: application/json" \
  -d "{\"username\":\"admin\",\"email\":\"admin@test.com\",\"password\":\"admin123\"}"
```

## 5. Test Authentication Flow

1. **Register** a user (see above)
2. **Login** to get a JWT token
3. **Use the token** to access protected endpoints

Example:
```bash
# Login
curl -X POST http://localhost/microservice-framework/api/auth/login \
  -H "Content-Type: application/json" \
  -d "{\"login\":\"admin\",\"password\":\"admin123\"}"

# Copy the token from response, then:
curl http://localhost/microservice-framework/api/auth/user \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

## 6. Explore the Services

### Auth Service Endpoints
- `POST /api/auth/register` - Create account
- `POST /api/auth/login` - Get JWT token
- `POST /api/auth/verify` - Verify token
- `GET /api/auth/user` - Get user info (requires token)

### Configuration Service Endpoints
- `GET /api/config` - Get all configurations
- `GET /api/config/app.name` - Get specific config
- `POST /api/config` - Create configuration
- `PUT /api/config/:key` - Update configuration

### Database Service Endpoints
- `GET /api/db/health` - Database health check
- `GET /api/db/tables` - List all tables
- `POST /api/db/query` - Execute SELECT query

## 7. View the Database

### Using phpMyAdmin
1. Go to `http://localhost/phpmyadmin/`
2. Look for database: `microservice_db`
3. You'll see tables:
   - `users` (created by Auth Service)
   - `configurations` (created by Config Service)

### Using MySQL Command Line
```bash
mysql -u root -p
USE microservice_db;
SHOW TABLES;
SELECT * FROM users;
```

## 🎉 You're Ready!

Your microservice framework is now running! Here's what you have:

### ✅ Working Components
- API Gateway routing all requests
- Auth Service with JWT authentication
- Configuration Service for settings
- Database Service for data operations
- Public Frontend for visitors
- User Dashboard for authenticated users

### 📚 Next Steps
- Read the full [README.md](README.md) for detailed documentation
- Check [API_EXAMPLES.md](API_EXAMPLES.md) for more API examples
- Explore the code in `/app/services/` to understand each microservice
- Add your own microservices by following the existing patterns

### 🛠️ Common Issues

**Issue: "Cannot connect to database"**
- Solution: Make sure MySQL is running in XAMPP

**Issue: "404 Not Found" errors**
- Solution: Check that mod_rewrite is enabled in Apache

**Issue: ".htaccess not working"**
- Solution: Verify `AllowOverride All` is set in Apache config

**Issue: "File not found" errors**
- Solution: Verify the project is in `c:\xampp\htdocs\microservice-framework\`

### 📞 Need Help?
- Check the inline code comments
- Review the README.md
- All services are self-documenting via `/api/services` endpoint

Enjoy building with your microservice framework! 🚀
