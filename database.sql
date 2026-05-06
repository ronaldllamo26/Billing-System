-- STUXZ Cybercafe Management & Billing System
-- Version 1.0.7 (AI Enhanced)

CREATE DATABASE IF NOT EXISTS stuxz_cybercafe;
USE stuxz_cybercafe;

-- Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100),
    role ENUM('Admin', 'User') DEFAULT 'User',
    balance DECIMAL(10, 2) DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- PC Units Table
CREATE TABLE IF NOT EXISTS pcs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pc_number INT NOT NULL UNIQUE,
    pc_type ENUM('Regular', 'VIP', 'E-Sports') DEFAULT 'Regular',
    status ENUM('Vacant', 'Occupied', 'AFK', 'Maintenance') DEFAULT 'Vacant',
    current_user_id INT NULL,
    time_remaining INT DEFAULT 0,
    start_time DATETIME NULL,
    end_time DATETIME NULL,
    rate_per_hour DECIMAL(10, 2) DEFAULT 25.00,
    FOREIGN KEY (current_user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Transactions & Billing Table
CREATE TABLE IF NOT EXISTS transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    pc_id INT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    transaction_type ENUM('Top-up', 'Rental', 'Order') NOT NULL,
    status ENUM('Pending', 'Completed', 'Cancelled') DEFAULT 'Completed',
    order_summary TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (pc_id) REFERENCES pcs(id) ON DELETE SET NULL
);

-- Inventory Table (Food & Merch)
CREATE TABLE IF NOT EXISTS inventory (
    id INT AUTO_INCREMENT PRIMARY KEY,
    item_name VARCHAR(100) NOT NULL,
    category ENUM('Food', 'Drinks', 'Merch') DEFAULT 'Food',
    price DECIMAL(10, 2) NOT NULL,
    stock_quantity INT DEFAULT 0,
    item_image VARCHAR(255) DEFAULT 'default_food.png',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Initial Admin (Password: admin123)
INSERT IGNORE INTO users (username, password, role) VALUES ('admin', 'admin123', 'Admin');

-- Initial PCs
INSERT IGNORE INTO pcs (pc_number, pc_type) VALUES 
(1, 'Regular'), (2, 'Regular'), (3, 'Regular'), (4, 'Regular'), (5, 'Regular'),
(6, 'VIP'), (7, 'VIP'), (8, 'VIP'), (9, 'E-Sports'), (10, 'E-Sports');
