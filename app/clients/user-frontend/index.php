<?php
/**
 * User Frontend - Dashboard
 */

$config = require __DIR__ . '/../../../config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $config['app']['name'] ?> - User Dashboard</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f7fa;
        }
        
        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px 40px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .navbar h1 {
            font-size: 1.8em;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        
        .auth-section {
            background: white;
            border-radius: 10px;
            padding: 40px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .auth-section.hidden {
            display: none;
        }
        
        .dashboard {
            display: none;
        }
        
        .dashboard.active {
            display: block;
        }
        
        .tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 30px;
            border-bottom: 2px solid #e0e0e0;
        }
        
        .tab {
            padding: 15px 30px;
            background: transparent;
            border: none;
            cursor: pointer;
            font-size: 1em;
            color: #666;
            border-bottom: 3px solid transparent;
            transition: all 0.3s;
        }
        
        .tab.active {
            color: #667eea;
            border-bottom-color: #667eea;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1em;
        }
        
        .btn {
            padding: 12px 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
            transition: transform 0.2s;
        }
        
        .btn:hover {
            transform: scale(1.05);
        }
        
        .btn-secondary {
            background: #6c757d;
        }
        
        .alert {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .stat-card h3 {
            color: #666;
            font-size: 0.9em;
            margin-bottom: 10px;
        }
        
        .stat-card .value {
            font-size: 2em;
            color: #667eea;
            font-weight: bold;
        }
        
        .user-info {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .user-info h2 {
            margin-bottom: 20px;
            color: #333;
        }
        
        .user-info table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .user-info td {
            padding: 12px;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .user-info td:first-child {
            font-weight: bold;
            color: #666;
            width: 200px;
        }
        
        pre {
            background: #2d2d2d;
            color: #00ff00;
            padding: 20px;
            border-radius: 5px;
            overflow-x: auto;
            font-family: 'Courier New', monospace;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>👤 User Dashboard</h1>
    </div>
    
    <div class="container">
        <!-- Login/Register Section -->
        <div id="authSection" class="auth-section">
            <div class="tabs">
                <button class="tab active" onclick="switchAuthTab('login')">Login</button>
                <button class="tab" onclick="switchAuthTab('register')">Register</button>
            </div>
            
            <div id="alertBox"></div>
            
            <!-- Login Form -->
            <div id="loginForm">
                <h2>Login to Your Account</h2>
                <form onsubmit="handleLogin(event)">
                    <div class="form-group">
                        <label>Username or Email</label>
                        <input type="text" id="loginUsername" required>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" id="loginPassword" required>
                    </div>
                    <button type="submit" class="btn">Login</button>
                </form>
            </div>
            
            <!-- Register Form -->
            <div id="registerForm" style="display:none;">
                <h2>Create New Account</h2>
                <form onsubmit="handleRegister(event)">
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" id="regUsername" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" id="regEmail" required>
                    </div>
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" id="regFullName">
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" id="regPassword" required minlength="8">
                    </div>
                    <button type="submit" class="btn">Register</button>
                </form>
            </div>
        </div>
        
        <!-- Dashboard Section (hidden until logged in) -->
        <div id="dashboardSection" class="dashboard">
            <div class="user-info">
                <h2>Welcome, <span id="userDisplayName"></span>!</h2>
                <button class="btn btn-secondary" onclick="handleLogout()">Logout</button>
            </div>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <h3>API Status</h3>
                    <div class="value" id="apiStatus">●</div>
                </div>
                <div class="stat-card">
                    <h3>Database Status</h3>
                    <div class="value" id="dbStatus">●</div>
                </div>
                <div class="stat-card">
                    <h3>Active Services</h3>
                    <div class="value" id="serviceCount">-</div>
                </div>
                <div class="stat-card">
                    <h3>Session Time</h3>
                    <div class="value" id="sessionTime">0m</div>
                </div>
            </div>
            
            <div class="user-info">
                <h2>Your Profile</h2>
                <table id="userProfile">
                    <tr><td>Loading...</td><td></td></tr>
                </table>
            </div>
            
            <div class="user-info">
                <h2>Your JWT Token</h2>
                <pre id="tokenDisplay"></pre>
            </div>
        </div>
    </div>
    
    <script>
        const API_BASE = 'api';
        let authToken = localStorage.getItem('authToken');
        let sessionStart = null;
        
        // Check if user is already logged in
        if (authToken) {
            verifyToken();
        }
        
        function switchAuthTab(tab) {
            document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
            event.target.classList.add('active');
            
            if (tab === 'login') {
                document.getElementById('loginForm').style.display = 'block';
                document.getElementById('registerForm').style.display = 'none';
            } else {
                document.getElementById('loginForm').style.display = 'none';
                document.getElementById('registerForm').style.display = 'block';
            }
        }
        
        async function handleRegister(e) {
            e.preventDefault();
            
            const data = {
                username: document.getElementById('regUsername').value,
                email: document.getElementById('regEmail').value,
                full_name: document.getElementById('regFullName').value,
                password: document.getElementById('regPassword').value
            };
            
            try {
                const response = await fetch(`${API_BASE}/auth/register`, {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify(data)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    authToken = result.data.token;
                    localStorage.setItem('authToken', authToken);
                    showAlert('Registration successful!', 'success');
                    setTimeout(() => showDashboard(), 1000);
                } else {
                    showAlert(result.error, 'error');
                }
            } catch (error) {
                showAlert('Registration failed: ' + error.message, 'error');
            }
        }
        
        async function handleLogin(e) {
            e.preventDefault();
            
            const data = {
                login: document.getElementById('loginUsername').value,
                password: document.getElementById('loginPassword').value
            };
            
            try {
                const response = await fetch(`${API_BASE}/auth/login`, {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify(data)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    authToken = result.data.token;
                    localStorage.setItem('authToken', authToken);
                    showAlert('Login successful!', 'success');
                    setTimeout(() => showDashboard(), 1000);
                } else {
                    showAlert(result.error, 'error');
                }
            } catch (error) {
                showAlert('Login failed: ' + error.message, 'error');
            }
        }
        
        async function verifyToken() {
            try {
                const response = await fetch(`${API_BASE}/auth/verify`, {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${authToken}`
                    }
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showDashboard();
                } else {
                    localStorage.removeItem('authToken');
                    authToken = null;
                }
            } catch (error) {
                localStorage.removeItem('authToken');
                authToken = null;
            }
        }
        
        function showDashboard() {
            document.getElementById('authSection').classList.add('hidden');
            document.getElementById('dashboardSection').classList.add('active');
            sessionStart = Date.now();
            loadDashboardData();
            startSessionTimer();
        }
        
        async function loadDashboardData() {
            // Load user info
            try {
                const response = await fetch(`${API_BASE}/auth/user`, {
                    headers: {'Authorization': `Bearer ${authToken}`}
                });
                const result = await response.json();
                
                if (result.success) {
                    const user = result.data.user;
                    document.getElementById('userDisplayName').textContent = user.username;
                    document.getElementById('userProfile').innerHTML = `
                        <tr><td>User ID</td><td>${user.id}</td></tr>
                        <tr><td>Username</td><td>${user.username}</td></tr>
                        <tr><td>Email</td><td>${user.email}</td></tr>
                        <tr><td>Full Name</td><td>${user.full_name || 'Not set'}</td></tr>
                        <tr><td>Created</td><td>${user.created_at}</td></tr>
                        <tr><td>Last Login</td><td>${user.last_login || 'Never'}</td></tr>
                        <tr><td>Status</td><td>${user.is_active ? 'Active' : 'Inactive'}</td></tr>
                    `;
                    
                    document.getElementById('tokenDisplay').textContent = authToken;
                }
            } catch (error) {
                console.error('Failed to load user data:', error);
            }
            
            // Check API health
            try {
                const response = await fetch(`${API_BASE}/health`);
                const result = await response.json();
                document.getElementById('apiStatus').textContent = '✓';
                document.getElementById('apiStatus').style.color = '#4CAF50';
                document.getElementById('serviceCount').textContent = Object.keys(result.data.services).length;
            } catch (error) {
                document.getElementById('apiStatus').textContent = '✗';
                document.getElementById('apiStatus').style.color = '#f44336';
            }
            
            // Check DB health
            try {
                const response = await fetch(`${API_BASE}/db/health`);
                const result = await response.json();
                document.getElementById('dbStatus').textContent = '✓';
                document.getElementById('dbStatus').style.color = '#4CAF50';
            } catch (error) {
                document.getElementById('dbStatus').textContent = '✗';
                document.getElementById('dbStatus').style.color = '#f44336';
            }
        }
        
        function startSessionTimer() {
            setInterval(() => {
                const elapsed = Math.floor((Date.now() - sessionStart) / 1000 / 60);
                document.getElementById('sessionTime').textContent = elapsed + 'm';
            }, 1000);
        }
        
        function handleLogout() {
            localStorage.removeItem('authToken');
            authToken = null;
            location.reload();
        }
        
        function showAlert(message, type) {
            const alertBox = document.getElementById('alertBox');
            alertBox.innerHTML = `<div class="alert alert-${type}">${message}</div>`;
            setTimeout(() => alertBox.innerHTML = '', 5000);
        }
    </script>
</body>
</html>
