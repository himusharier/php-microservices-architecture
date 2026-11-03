<?php
/**
 * Public Frontend - Landing Page
 */

$config = require __DIR__ . '/../../../config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $config['app']['name'] ?> - Public</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .container {
            background: white;
            max-width: 1200px;
            width: 100%;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px;
            text-align: center;
        }
        
        .header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
        }
        
        .header p {
            font-size: 1.2em;
            opacity: 0.9;
        }
        
        .content {
            padding: 40px;
        }
        
        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin: 30px 0;
        }
        
        .feature-card {
            padding: 30px;
            background: #f8f9fa;
            border-radius: 10px;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .feature-card h3 {
            color: #667eea;
            margin-bottom: 15px;
            font-size: 1.5em;
        }
        
        .feature-card p {
            color: #666;
            line-height: 1.6;
        }
        
        .cta-section {
            text-align: center;
            margin-top: 40px;
            padding: 40px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 10px;
        }
        
        .cta-section h2 {
            color: white;
            margin-bottom: 20px;
        }
        
        .btn {
            display: inline-block;
            padding: 15px 40px;
            margin: 10px;
            background: white;
            color: #667eea;
            text-decoration: none;
            border-radius: 50px;
            font-weight: bold;
            transition: all 0.3s;
        }
        
        .btn:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 20px rgba(0,0,0,0.2);
        }
        
        .api-demo {
            margin-top: 40px;
            padding: 30px;
            background: #f8f9fa;
            border-radius: 10px;
        }
        
        .api-demo h3 {
            margin-bottom: 20px;
            color: #333;
        }
        
        .api-endpoint {
            display: flex;
            align-items: center;
            margin: 10px 0;
            padding: 15px;
            background: white;
            border-left: 4px solid #667eea;
            border-radius: 5px;
        }
        
        .api-endpoint code {
            flex: 1;
            color: #667eea;
            font-family: 'Courier New', monospace;
        }
        
        .api-endpoint button {
            padding: 8px 20px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }
        
        .api-endpoint button:hover {
            background: #764ba2;
        }
        
        #apiResponse {
            margin-top: 20px;
            padding: 20px;
            background: #2d2d2d;
            color: #00ff00;
            border-radius: 5px;
            font-family: 'Courier New', monospace;
            white-space: pre-wrap;
            max-height: 400px;
            overflow-y: auto;
            display: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🚀 <?= $config['app']['name'] ?></h1>
            <p>Raw PHP Microservice Architecture on Apache & MySQL</p>
        </div>
        
        <div class="content">
            <div class="features">
                <div class="feature-card">
                    <h3>🔐 Authentication Service</h3>
                    <p>Secure JWT-based authentication with user registration, login, and token management.</p>
                </div>
                
                <div class="feature-card">
                    <h3>⚙️ Configuration Service</h3>
                    <p>Centralized configuration management for all your application settings and parameters.</p>
                </div>
                
                <div class="feature-card">
                    <h3>🗄️ Database Service</h3>
                    <p>Robust database operations with health monitoring and query execution capabilities.</p>
                </div>
                
                <div class="feature-card">
                    <h3>🌐 API Gateway</h3>
                    <p>Single entry point for all microservices with intelligent routing and load balancing.</p>
                </div>
                
                <div class="feature-card">
                    <h3>🔄 RESTful APIs</h3>
                    <p>Clean, well-documented REST APIs following industry best practices.</p>
                </div>
                
                <div class="feature-card">
                    <h3>📊 Scalable Architecture</h3>
                    <p>Microservice-based design that scales horizontally with your business needs.</p>
                </div>
            </div>
            
            <div class="api-demo">
                <h3>Try Our API Endpoints:</h3>
                
                <div class="api-endpoint">
                    <code>GET /api/health</code>
                    <button onclick="testAPI('api/health')">Test</button>
                </div>
                
                <div class="api-endpoint">
                    <code>GET /api/services</code>
                    <button onclick="testAPI('api/services')">Test</button>
                </div>
                
                <div class="api-endpoint">
                    <code>GET /api/db/health</code>
                    <button onclick="testAPI('api/db/health')">Test</button>
                </div>
                
                <div class="api-endpoint">
                    <code>GET /api/config</code>
                    <button onclick="testAPI('api/config')">Test</button>
                </div>
                
                <div id="apiResponse"></div>
            </div>
            
            <div class="cta-section">
                <h2>Ready to Get Started?</h2>
                <a href="user/" class="btn">User Dashboard</a>
                <a href="api/services" class="btn">API Documentation</a>
            </div>
        </div>
    </div>
    
    <script>
        async function testAPI(endpoint) {
            const responseDiv = document.getElementById('apiResponse');
            responseDiv.style.display = 'block';
            responseDiv.textContent = 'Loading...';
            
            try {
                const response = await fetch(endpoint);
                const data = await response.json();
                responseDiv.textContent = JSON.stringify(data, null, 2);
            } catch (error) {
                responseDiv.textContent = 'Error: ' + error.message;
            }
        }
    </script>
</body>
</html>
