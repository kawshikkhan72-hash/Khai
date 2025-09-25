-- Schema and sample data for Food Delivery
CREATE DATABASE IF NOT EXISTS food_delivery DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE food_delivery;

-- Users
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(120) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  phone VARCHAR(30) NOT NULL,
  role ENUM('customer','delivery','admin') NOT NULL DEFAULT 'customer'
) ENGINE=InnoDB;

-- Delivery personnel
CREATE TABLE IF NOT EXISTS delivery (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(120) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  phone VARCHAR(30) NOT NULL,
  vehicle_no VARCHAR(50) DEFAULT NULL,
  status ENUM('inactive','active') NOT NULL DEFAULT 'active'
) ENGINE=InnoDB;

-- Restaurants
CREATE TABLE IF NOT EXISTS restaurants (
  id INT AUTO_INCREMENT PRIMARY KEY,
  owner_id INT NULL,
  name VARCHAR(150) NOT NULL,
  email VARCHAR(120) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  location VARCHAR(200) NOT NULL,
  phone VARCHAR(30) NOT NULL,
  status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB;

-- Foods
CREATE TABLE IF NOT EXISTS foods (
  id INT AUTO_INCREMENT PRIMARY KEY,
  restaurant_id INT NOT NULL,
  name VARCHAR(150) NOT NULL,
  description TEXT,
  price DECIMAL(10,2) NOT NULL,
  image VARCHAR(255) DEFAULT NULL,
  FOREIGN KEY (restaurant_id) REFERENCES restaurants(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Orders
CREATE TABLE IF NOT EXISTS orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  restaurant_id INT NOT NULL,
  delivery_id INT NULL,
  total_price DECIMAL(10,2) NOT NULL,
  status VARCHAR(50) NOT NULL DEFAULT 'pending',
  order_date DATETIME NOT NULL,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (restaurant_id) REFERENCES restaurants(id) ON DELETE CASCADE,
  FOREIGN KEY (delivery_id) REFERENCES delivery(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Order Items
CREATE TABLE IF NOT EXISTS order_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  food_id INT NOT NULL,
  quantity INT NOT NULL,
  price DECIMAL(10,2) NOT NULL,
  FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
  FOREIGN KEY (food_id) REFERENCES foods(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Sample admin user (password: admin123)
INSERT INTO users (name, email, password, phone, role) VALUES
('Admin', 'admin@food.local', '$2y$10$QqH7h1lFppkYbVw2m6fAwuqphK0z7sS0S7oWb5mYQeQpZB9z4f0Y2', '0000000000', 'admin')
ON DUPLICATE KEY UPDATE email = email;

-- Sample customers (password: customer123)
INSERT INTO users (name, email, password, phone, role) VALUES
('John Doe', 'john@example.com', '$2y$10$2U3fQkQG3wB2xF3wK6ZVQe2Dbw4wIuSx4n2Qx1a0M3t5otH1bA9rK', '1234567890', 'customer'),
('Jane Smith', 'jane@example.com', '$2y$10$2U3fQkQG3wB2xF3wK6ZVQe2Dbw4wIuSx4n2Qx1a0M3t5otH1bA9rK', '0987654321', 'customer')
ON DUPLICATE KEY UPDATE email = email;

-- Sample delivery (password: delivery123)
INSERT INTO delivery (name, email, password, phone, vehicle_no, status) VALUES
('Rider One', 'rider1@food.local', '$2y$10$J7H5k9Ew9l7m0j8QkLs9UeHn4b9nN3p2s4d6f8g1h2j3k4l5m6n7o', '9991112222', 'ABC-123', 'active')
ON DUPLICATE KEY UPDATE email = email;

-- Sample restaurants (password: rest123)
INSERT INTO restaurants (owner_id, name, email, password, location, phone, status) VALUES
(NULL, 'Pizza Place', 'pizza@food.local', '$2y$10$6qv2IY2r9eW4v2Yz8tPpUeE7oFv0bYpQkR6sW8uE9tY3rU6iO1lXa', 'Downtown', '5550001111', 'approved'),
(NULL, 'Burger Hub', 'burger@food.local', '$2y$10$6qv2IY2r9eW4v2Yz8tPpUeE7oFv0bYpQkR6sW8uE9tY3rU6iO1lXa', 'Uptown', '5550002222', 'approved')
ON DUPLICATE KEY UPDATE email = email;

-- Sample foods
INSERT INTO foods (restaurant_id, name, description, price) VALUES
((SELECT id FROM restaurants WHERE email='pizza@food.local'), 'Margherita Pizza', 'Classic cheese pizza', 8.99),
((SELECT id FROM restaurants WHERE email='pizza@food.local'), 'Pepperoni Pizza', 'Pepperoni and cheese', 10.99),
((SELECT id FROM restaurants WHERE email='burger@food.local'), 'Classic Burger', 'Beef patty with lettuce and tomato', 7.49),
((SELECT id FROM restaurants WHERE email='burger@food.local'), 'Cheese Burger', 'Beef patty with cheese', 7.99);

