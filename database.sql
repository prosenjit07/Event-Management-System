-- Create the database
CREATE DATABASE IF NOT EXISTS your_database;
USE your_database;

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create categories table
CREATE TABLE IF NOT EXISTS categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create items table
CREATE TABLE IF NOT EXISTS items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    category_id INT,
    price DECIMAL(10, 2) NOT NULL,
    stock_quantity INT NOT NULL DEFAULT 0,
    image_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Create orders table
CREATE TABLE IF NOT EXISTS orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'processing', 'completed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Create order_items table (junction table for orders and items)
CREATE TABLE IF NOT EXISTS order_items (
    order_id INT,
    item_id INT,
    quantity INT NOT NULL,
    price_at_time DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (order_id, item_id),
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (item_id) REFERENCES items(id) ON DELETE RESTRICT
);

-- Insert sample categories
INSERT INTO categories (name, description) VALUES
    ('Electronics', 'Electronic devices and accessories'),
    ('Clothing', 'Apparel and fashion items'),
    ('Books', 'Books and publications'),
    ('Home & Garden', 'Home decoration and garden supplies');

-- Insert sample items
INSERT INTO items (name, description, category_id, price, stock_quantity) VALUES
    ('Smartphone', 'Latest model smartphone', 1, 699.99, 50),
    ('Laptop', 'High-performance laptop', 1, 1299.99, 30),
    ('T-shirt', 'Cotton casual t-shirt', 2, 19.99, 100),
    ('Novel', 'Bestseller fiction book', 3, 14.99, 75),
    ('Plant Pot', 'Ceramic plant pot', 4, 24.99, 60);

-- Insert sample user
INSERT INTO users (username, email, password) VALUES
    ('john_doe', 'john@example.com', '$2y$10$example_hashed_password'),
    ('jane_smith', 'jane@example.com', '$2y$10$example_hashed_password');

-- Create indexes for better performance
CREATE INDEX idx_items_category ON items(category_id);
CREATE INDEX idx_orders_user ON orders(user_id);
CREATE INDEX idx_items_name ON items(name);