-- Database: user_portal

CREATE DATABASE IF NOT EXISTS user_portal;
USE user_portal;

-- Table structure for users
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(100) NOT NULL,
    user_type ENUM('admin', 'basic') NOT NULL DEFAULT 'basic',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default admin user (username: admin, password: admin)
INSERT INTO users (username, email, password, user_type)
VALUES ('admin', 'admin@marked.com', 'admin', 'admin')
ON DUPLICATE KEY UPDATE email='admin@marked.com';