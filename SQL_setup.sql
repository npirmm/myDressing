CREATE DATABASE myDressing;

USE myDressing;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'standard', 'viewer') NOT NULL,
    2fa_enabled BOOLEAN DEFAULT 0,
    2fa_method ENUM('otp', 'email') DEFAULT NULL,
    2fa_secret VARCHAR(255) DEFAULT NULL,
    email_verified BOOLEAN DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    action VARCHAR(255) NOT NULL,
    details TEXT DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE article_photos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    article_id INT NOT NULL,
    photo_name VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
