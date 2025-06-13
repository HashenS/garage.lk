-- Drop database if exists and create new one
DROP DATABASE IF EXISTS garage_lk;
CREATE DATABASE garage_lk;
USE garage_lk;

-- Create users table
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(15) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('customer', 'garage', 'admin') NOT NULL DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create garages table
CREATE TABLE garages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    business_name VARCHAR(100) NOT NULL,
    business_registration_number VARCHAR(50) NOT NULL,
    address TEXT NOT NULL,
    latitude DECIMAL(10, 8) NOT NULL,
    longitude DECIMAL(11, 8) NOT NULL,
    phone VARCHAR(15) NOT NULL,
    email VARCHAR(100) NOT NULL,
    description TEXT,
    verification_status ENUM('pending', 'verified', 'rejected') NOT NULL DEFAULT 'pending',
    verification_notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_verification_status (verification_status),
    INDEX idx_location (latitude, longitude)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create documents table
CREATE TABLE documents (
    id INT PRIMARY KEY AUTO_INCREMENT,
    garage_id INT NOT NULL,
    document_type ENUM('business_registration', 'owner_nic', 'utility_bill', 'garage_photo') NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    verification_status ENUM('pending', 'verified', 'rejected') NOT NULL DEFAULT 'pending',
    verification_notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (garage_id) REFERENCES garages(id) ON DELETE CASCADE,
    INDEX idx_document_type (document_type),
    INDEX idx_verification_status (verification_status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create services table
CREATE TABLE services (
    id INT PRIMARY KEY AUTO_INCREMENT,
    garage_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    duration INT NOT NULL COMMENT 'Duration in minutes',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (garage_id) REFERENCES garages(id) ON DELETE CASCADE,
    INDEX idx_garage_id (garage_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create bookings table
CREATE TABLE bookings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    garage_id INT NOT NULL,
    service_id INT NOT NULL,
    booking_date DATE NOT NULL,
    booking_time TIME NOT NULL,
    status ENUM('pending', 'confirmed', 'completed', 'cancelled') NOT NULL DEFAULT 'pending',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (garage_id) REFERENCES garages(id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE,
    INDEX idx_booking_date (booking_date),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create reviews table
CREATE TABLE reviews (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    garage_id INT NOT NULL,
    booking_id INT NOT NULL,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (garage_id) REFERENCES garages(id) ON DELETE CASCADE,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    INDEX idx_garage_id (garage_id),
    INDEX idx_rating (rating)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create spare_parts table
CREATE TABLE spare_parts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    garage_id INT NOT NULL,
    part_name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    stock_quantity INT NOT NULL DEFAULT 0,
    is_available BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (garage_id) REFERENCES garages(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create orders table
CREATE TABLE orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    garage_id INT NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'processing', 'completed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (garage_id) REFERENCES garages(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create order_items table
CREATE TABLE order_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    spare_part_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (spare_part_id) REFERENCES spare_parts(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create admin_logs table
CREATE TABLE admin_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    admin_id INT NOT NULL,
    action VARCHAR(255) NOT NULL,
    details TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample data

-- Insert admin user (password: admin123)
INSERT INTO users (first_name, last_name, email, phone, password, role) VALUES
('Admin', 'User', 'admin@garage.lk', '+94711234567', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Insert sample customers (password: password123)
INSERT INTO users (first_name, last_name, email, phone, password, role) VALUES
('John', 'Doe', 'john@example.com', '+94712345678', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer'),
('Jane', 'Smith', 'jane@example.com', '+94723456789', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer');

-- Insert sample garage owners (password: garage123)
INSERT INTO users (first_name, last_name, email, phone, password, role) VALUES
('Sam', 'Wilson', 'sam@autocare.lk', '+94734567890', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'garage'),
('Mike', 'Johnson', 'mike@carmasters.lk', '+94745678901', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'garage');

-- Insert sample garages
INSERT INTO garages (user_id, business_name, business_registration_number, address, latitude, longitude, phone, email, description, verification_status) VALUES
(3, 'AutoCare Lanka', 'BR123456', 'No. 123, Galle Road, Colombo 06', 6.874657, 79.862939, '+94734567890', 'info@autocare.lk', 'Full service auto care center with modern equipment', 'verified'),
(4, 'Car Masters', 'BR789012', 'No. 456, Kandy Road, Kadawatha', 7.009517, 79.958084, '+94745678901', 'info@carmasters.lk', 'Specialized in European vehicle repairs', 'verified');

-- Insert sample services
INSERT INTO services (garage_id, name, description, price, duration) VALUES
(1, 'Full Service', 'Complete vehicle service including oil change, filter replacement, and general inspection', 15000.00, 180),
(1, 'Oil Change', 'Engine oil and filter replacement', 8000.00, 60),
(2, 'Brake Service', 'Brake pad replacement and brake system inspection', 12000.00, 120),
(2, 'Engine Diagnostic', 'Complete engine diagnostic using computerized system', 5000.00, 60);

-- Insert sample spare parts
INSERT INTO spare_parts (garage_id, part_name, description, price, stock_quantity) VALUES
(1, 'Oil Filter', 'Genuine Toyota oil filter', 1500.00, 50),
(1, 'Air Filter', 'High-performance air filter', 2500.00, 30),
(2, 'Brake Pads', 'Front brake pads for Toyota Corolla', 8000.00, 20),
(2, 'Spark Plugs', 'NGK spark plugs set of 4', 4000.00, 40);

-- Insert sample bookings
INSERT INTO bookings (user_id, garage_id, service_id, booking_date, booking_time, status) VALUES
(1, 1, 1, '2024-03-20', '10:00:00', 'confirmed'),
(2, 2, 3, '2024-03-21', '14:00:00', 'pending');

-- Insert sample reviews
INSERT INTO reviews (user_id, garage_id, booking_id, rating, comment) VALUES
(1, 1, 1, 5, 'Excellent service! Very professional team.'),
(2, 2, 2, 4, 'Good service but slightly expensive');

-- Insert sample orders
INSERT INTO orders (user_id, garage_id, total_amount, status) VALUES
(1, 1, 4000.00, 'completed'),
(2, 2, 12000.00, 'processing');

-- Insert sample order items
INSERT INTO order_items (order_id, spare_part_id, quantity, price) VALUES
(1, 1, 2, 1500.00),
(1, 2, 1, 2500.00),
(2, 3, 1, 8000.00),
(2, 4, 1, 4000.00);

-- Insert sample admin logs
INSERT INTO admin_logs (admin_id, action, details) VALUES
(1, 'Verified Garage', 'Verified AutoCare Lanka garage registration'),
(1, 'Verified Garage', 'Verified Car Masters garage registration'); 