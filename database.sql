SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE DATABASE IF NOT EXISTS web
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE web;

DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS customers;
DROP TABLE IF EXISTS suppliers;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    user_id       INT PRIMARY KEY AUTO_INCREMENT,
    username      VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role          ENUM('Admin', 'Staff', 'Customer') NOT NULL DEFAULT 'Staff',
    email         VARCHAR(100) UNIQUE,
    status        ENUM('Active', 'Inactive') NOT NULL DEFAULT 'Active',
    created_at    DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at    DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE customers (
    customer_id   INT PRIMARY KEY AUTO_INCREMENT,
    user_id       INT UNIQUE,
    customer_name VARCHAR(150) NOT NULL,
    phone         VARCHAR(20),
    address       VARCHAR(255),
    created_at    DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at    DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_customers_users FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL ON UPDATE CASCADE
);

CREATE TABLE categories (
    category_id   INT PRIMARY KEY AUTO_INCREMENT,
    category_name VARCHAR(100) NOT NULL UNIQUE,
    description   TEXT,
    status        ENUM('Active', 'Inactive') NOT NULL DEFAULT 'Active',
    created_at    DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at    DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE suppliers (
    supplier_id   INT PRIMARY KEY AUTO_INCREMENT,
    supplier_name VARCHAR(150) NOT NULL,
    phone         VARCHAR(20),
    email         VARCHAR(100),
    address       VARCHAR(255),
    status        ENUM('Active', 'Inactive') NOT NULL DEFAULT 'Active',
    created_at    DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at    DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE products (
    product_id     INT PRIMARY KEY AUTO_INCREMENT,
    product_name   VARCHAR(150) NOT NULL,
    category_id    INT,
    supplier_id    INT,
    unit_price     DECIMAL(12,2) NOT NULL,
    old_price      DECIMAL(12,2),
    stock_quantity INT NOT NULL DEFAULT 0,
    main_image     VARCHAR(255),
    description    TEXT,
    status         ENUM('Active', 'Inactive') NOT NULL DEFAULT 'Active',
    created_at     DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at     DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_products_categories FOREIGN KEY (category_id) REFERENCES categories(category_id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT fk_products_suppliers FOREIGN KEY (supplier_id) REFERENCES suppliers(supplier_id) ON DELETE SET NULL ON UPDATE CASCADE
);

CREATE INDEX idx_users_role ON users(role);
CREATE INDEX idx_users_status ON users(status);
CREATE INDEX idx_products_category ON products(category_id);
CREATE INDEX idx_products_supplier ON products(supplier_id);
CREATE INDEX idx_products_status ON products(status);
