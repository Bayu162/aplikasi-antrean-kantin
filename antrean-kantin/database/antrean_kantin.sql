-- Database sederhana aplikasi Kantin.Antri
-- Import melalui phpMyAdmin > Import.
CREATE DATABASE IF NOT EXISTS antrean_kantin CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE antrean_kantin;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS queue_counters;
DROP TABLE IF EXISTS menus;
DROP TABLE IF EXISTS admins; -- hapus tabel admin dari versi sebelumnya
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE menus (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description VARCHAR(255) NOT NULL DEFAULT '',
    price INT UNSIGNED NOT NULL,
    is_available TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE queue_counters (
    queue_date DATE PRIMARY KEY,
    last_number INT UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB;

CREATE TABLE orders (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    queue_date DATE NOT NULL,
    queue_number INT UNSIGNED NOT NULL,
    customer_name VARCHAR(100) NOT NULL,
    class_name VARCHAR(40) NOT NULL,
    notes VARCHAR(300) NOT NULL DEFAULT '',
    total INT UNSIGNED NOT NULL DEFAULT 0,
    status ENUM('menunggu','diproses','siap_diambil','diterima','selesai','dibatalkan') NOT NULL DEFAULT 'menunggu',
    received_at DATETIME NULL,
    rating TINYINT UNSIGNED NULL,
    rating_comment VARCHAR(300) NULL,
    rated_at DATETIME NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_daily_queue (queue_date, queue_number),
    KEY idx_orders_date (queue_date)
) ENGINE=InnoDB;

CREATE TABLE order_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id BIGINT UNSIGNED NOT NULL,
    menu_id INT UNSIGNED NULL,
    menu_name VARCHAR(100) NOT NULL,
    price INT UNSIGNED NOT NULL,
    quantity SMALLINT UNSIGNED NOT NULL,
    subtotal INT UNSIGNED NOT NULL,
    CONSTRAINT fk_order_items_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    CONSTRAINT fk_order_items_menu FOREIGN KEY (menu_id) REFERENCES menus(id) ON DELETE SET NULL,
    KEY idx_order_items_order (order_id)
) ENGINE=InnoDB;

INSERT INTO menus (name, description, price, is_available) VALUES
('Nasi goreng', 'Nasi goreng sederhana dengan telur', 12000, 1),
('Mie goreng', 'Mie goreng hangat porsi kantin', 10000, 1),
('Roti isi', 'Roti isi cokelat atau keju', 7000, 1),
('Es teh', 'Es teh manis segar', 4000, 1),
('Air mineral', 'Air mineral kemasan', 4000, 1),
('Pisang goreng', 'Pisang goreng hangat, 3 potong', 6000, 1);
