-- Microservice Framework Database Setup
-- This file contains all table schemas
-- Tables are auto-created by services, but this provides a manual reference

-- Create database
CREATE DATABASE IF NOT EXISTS microservice_db;
USE microservice_db;

-- Users table (Auth Service)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    is_active BOOLEAN DEFAULT TRUE,
    INDEX idx_username (username),
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Configurations table (Configuration Service)
CREATE TABLE IF NOT EXISTS configurations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    config_key VARCHAR(100) UNIQUE NOT NULL,
    config_value TEXT,
    description VARCHAR(255),
    is_encrypted BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_config_key (config_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default configurations
INSERT IGNORE INTO configurations (config_key, config_value, description) VALUES
('app.name', 'Microservice Framework', 'Application name'),
('app.maintenance_mode', 'false', 'Maintenance mode flag'),
('app.max_upload_size', '10485760', 'Maximum upload size in bytes (10MB)'),
('security.password_min_length', '8', 'Minimum password length'),
('security.session_timeout', '3600', 'Session timeout in seconds');

-- Sample admin user (password: admin123)
-- Uncomment to create a default admin user
-- INSERT IGNORE INTO users (username, email, password, full_name) VALUES
-- ('admin', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator');
