<?php
// Database Setup Script
// Run this once to create the database and tables

require_once __DIR__ . '/config/config.php';

try {
    // Connect to MySQL (without selecting database)
    $pdo = new PDO(
        'mysql:host=' . DB_HOST,
        DB_USER,
        DB_PASS
    );
    
    // Create Database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME);
    
    // Select Database
    $pdo->exec("USE " . DB_NAME);
    
    // Create users table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INT PRIMARY KEY AUTO_INCREMENT,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            role ENUM('admin', 'seller') NOT NULL DEFAULT 'seller',
            status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )
    ");
    
    // Create products table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS products (
            id INT PRIMARY KEY AUTO_INCREMENT,
            name VARCHAR(255) NOT NULL,
            description TEXT,
            base_price DECIMAL(10, 2) NOT NULL,
            stock_quantity INT NOT NULL DEFAULT 0,
            image VARCHAR(255),
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )
    ");
    
    // Create purchase_requests table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS purchase_requests (
            id INT PRIMARY KEY AUTO_INCREMENT,
            seller_id INT NOT NULL,
            product_id INT NOT NULL,
            quantity INT NOT NULL,
            status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (seller_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
        )
    ");
    
    // Create admin account
    $adminPassword = password_hash('admin123', PASSWORD_BCRYPT);
    $pdo->exec("
        INSERT IGNORE INTO users (name, email, password, role, status) 
        VALUES ('Admin', 'admin@elo.com', '$adminPassword', 'admin', 'approved')
    ");
    
    echo "Database setup completed successfully!<br>";
    echo "Admin Email: admin@elo.com<br>";
    echo "Admin Password: admin123<br>";
    
} catch (PDOException $e) {
    echo "Setup Error: " . $e->getMessage();
}
?>
