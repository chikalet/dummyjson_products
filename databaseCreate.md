show databases;

create database dummyjson_products;

USE dummyjson_products;

CREATE TABLE products (
                          id INT PRIMARY KEY AUTO_INCREMENT,
                          product_id INT NOT NULL,
                          title VARCHAR(255),
                          description TEXT,
                          price DECIMAL(10, 2),
                          brand VARCHAR(255),
                          category VARCHAR(255),
                          thumbnail VARCHAR(255),
                          created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
