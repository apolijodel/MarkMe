-- MarkMe: Customizable Bookmark E-Commerce System
-- Database schema + demonstration seed data.

CREATE DATABASE IF NOT EXISTS markme_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE markme_db;

SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------
-- users: customer and administrator accounts
-- ---------------------------------------------------------------------
CREATE TABLE users (
    user_id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    role            ENUM('customer', 'admin') NOT NULL DEFAULT 'customer',
    username        VARCHAR(50)  NOT NULL UNIQUE,
    email           VARCHAR(150) NOT NULL UNIQUE,
    password_hash   VARCHAR(255) NOT NULL,
    full_name       VARCHAR(150) NOT NULL,
    contact_number  VARCHAR(30)  NULL,
    address         VARCHAR(255) NULL,
    is_active       TINYINT(1)   NOT NULL DEFAULT 1,
    created_at      TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_users_role (role)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- categories: product category records
-- ---------------------------------------------------------------------
CREATE TABLE categories (
    category_id     INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name            VARCHAR(100) NOT NULL UNIQUE,
    description     VARCHAR(255) NULL,
    created_at      TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- shapes: admin-managed bookmark shape options
-- ---------------------------------------------------------------------
CREATE TABLE shapes (
    shape_id        INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name            VARCHAR(100) NOT NULL UNIQUE,
    image_path      VARCHAR(255) NOT NULL,
    created_at      TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- designs: admin-managed template designs
-- ---------------------------------------------------------------------
CREATE TABLE designs (
    design_id       INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name            VARCHAR(100) NOT NULL UNIQUE,
    image_path      VARCHAR(255) NOT NULL,
    created_at      TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- products: product information, price, stock, image, category reference
-- ---------------------------------------------------------------------
CREATE TABLE products (
    product_id      INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_id     INT UNSIGNED NOT NULL,
    name            VARCHAR(150) NOT NULL,
    description     TEXT NULL,
    price           DECIMAL(10,2) NOT NULL,
    stock_quantity  INT UNSIGNED NOT NULL DEFAULT 0,
    image_path      VARCHAR(255) NOT NULL,
    is_customizable TINYINT(1)   NOT NULL DEFAULT 1,
    created_at      TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_products_category FOREIGN KEY (category_id)
        REFERENCES categories(category_id) ON DELETE RESTRICT ON UPDATE CASCADE,
    INDEX idx_products_category (category_id),
    INDEX idx_products_name (name)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- carts: one active cart per customer
-- ---------------------------------------------------------------------
CREATE TABLE carts (
    cart_id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id         INT UNSIGNED NOT NULL UNIQUE,
    created_at      TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_carts_user FOREIGN KEY (user_id)
        REFERENCES users(user_id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- cart_items: products (and chosen customization) inside a cart
-- ---------------------------------------------------------------------
CREATE TABLE cart_items (
    cart_item_id      INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cart_id           INT UNSIGNED NOT NULL,
    product_id        INT UNSIGNED NOT NULL,
    quantity          INT UNSIGNED NOT NULL DEFAULT 1,
    shape_id          INT UNSIGNED NULL,
    design_id         INT UNSIGNED NULL,
    custom_text       VARCHAR(120) NULL,
    custom_image_path VARCHAR(255) NULL,
    created_at        TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_cart_items_cart FOREIGN KEY (cart_id)
        REFERENCES carts(cart_id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_cart_items_product FOREIGN KEY (product_id)
        REFERENCES products(product_id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_cart_items_shape FOREIGN KEY (shape_id)
        REFERENCES shapes(shape_id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT fk_cart_items_design FOREIGN KEY (design_id)
        REFERENCES designs(design_id) ON DELETE SET NULL ON UPDATE CASCADE,
    INDEX idx_cart_items_cart (cart_id),
    INDEX idx_cart_items_product (product_id)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- orders: order header, customer, address, total, status, timestamps
-- ---------------------------------------------------------------------
CREATE TABLE orders (
    order_id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id           INT UNSIGNED NOT NULL,
    full_name         VARCHAR(150) NOT NULL,
    contact_number    VARCHAR(30)  NOT NULL,
    delivery_address  VARCHAR(255) NOT NULL,
    payment_method    ENUM('Cash on Delivery', 'Bank Transfer') NOT NULL DEFAULT 'Cash on Delivery',
    total_amount      DECIMAL(10,2) NOT NULL,
    status            ENUM('Pending', 'Processing', 'Ready for Delivery', 'Completed', 'Cancelled')
                          NOT NULL DEFAULT 'Pending',
    created_at        TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at        TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_orders_user FOREIGN KEY (user_id)
        REFERENCES users(user_id) ON DELETE RESTRICT ON UPDATE CASCADE,
    INDEX idx_orders_user (user_id),
    INDEX idx_orders_status (status)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- order_items: products, quantities, and recorded unit prices per order
-- Name/price fields are snapshots taken at order time so historical
-- orders stay accurate even if the product/shape/design is later edited.
-- ---------------------------------------------------------------------
CREATE TABLE order_items (
    order_item_id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id              INT UNSIGNED NOT NULL,
    product_id            INT UNSIGNED NULL,
    product_name_snapshot VARCHAR(150) NOT NULL,
    unit_price            DECIMAL(10,2) NOT NULL,
    quantity              INT UNSIGNED NOT NULL,
    shape_id              INT UNSIGNED NULL,
    shape_name_snapshot   VARCHAR(100) NULL,
    design_id             INT UNSIGNED NULL,
    design_name_snapshot  VARCHAR(100) NULL,
    custom_text           VARCHAR(120) NULL,
    custom_image_path     VARCHAR(255) NULL,
    line_total            DECIMAL(10,2) NOT NULL,
    CONSTRAINT fk_order_items_order FOREIGN KEY (order_id)
        REFERENCES orders(order_id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_order_items_product FOREIGN KEY (product_id)
        REFERENCES products(product_id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT fk_order_items_shape FOREIGN KEY (shape_id)
        REFERENCES shapes(shape_id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT fk_order_items_design FOREIGN KEY (design_id)
        REFERENCES designs(design_id) ON DELETE SET NULL ON UPDATE CASCADE,
    INDEX idx_order_items_order (order_id),
    INDEX idx_order_items_product (product_id)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- feedback_messages: contact-us / feedback submissions (open to guests)
-- ---------------------------------------------------------------------
CREATE TABLE feedback_messages (
    feedback_id     INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name            VARCHAR(150) NOT NULL,
    email           VARCHAR(150) NOT NULL,
    message         TEXT NOT NULL,
    created_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

SET FOREIGN_KEY_CHECKS = 1;

-- ---------------------------------------------------------------------
-- Seed data
-- ---------------------------------------------------------------------

-- Admin account: username "admin", password "Admin@123" (change after import).
INSERT INTO users (role, username, email, password_hash, full_name, contact_number, address, is_active) VALUES
('admin', 'admin', 'admin@markme.test', '$2y$10$AQzVaZ3B2WDpw2SPe0biyegyhM7YHYbCufVG/ptoQSEsPkENjfpXu', 'MarkMe Administrator', '09171234567', 'MarkMe HQ', 1);

-- Demo customer account: username "juana", password "Customer@123".
INSERT INTO users (role, username, email, password_hash, full_name, contact_number, address, is_active) VALUES
('customer', 'juana', 'juana@example.test', '$2y$10$RXTrHLS6a.QykwbmCZZndel9JEaZwg9oUbTjXZRiuas6QopEqWiza', 'Juana Dela Cruz', '09179876543', '123 Rizal St, Manila', 1);

INSERT INTO categories (name, description) VALUES
('Classic Bookmarks', 'Ready-made bookmark designs, no customization required'),
('Personalized Bookmarks', 'Fully customizable bookmarks with your own photo and text'),
('Gift Sets', 'Bundled bookmark sets for gifting');

INSERT INTO shapes (name, image_path) VALUES
('Rectangular', 'rectangular.svg'),
('Rounded', 'rounded.svg'),
('Heart', 'heart.svg'),
('Bookworm Tassel', 'tassel.svg');

INSERT INTO designs (name, image_path) VALUES
('Floral', 'floral.svg'),
('Minimalist Lines', 'minimalist.svg'),
('Starry Night', 'starry-night.svg'),
('Plain Kraft', 'plain-kraft.svg');

INSERT INTO products (category_id, name, description, price, stock_quantity, image_path, is_customizable) VALUES
(2, 'Photo Memory Bookmark', 'Upload your favorite photo and a short message on a durable laminated bookmark.', 99.00, 50, 'photo-memory.svg', 1),
(2, 'Name & Quote Bookmark', 'Add your name and a favorite quote to a custom-shaped bookmark.', 79.00, 40, 'name-quote.svg', 1),
(1, 'Classic Floral Bookmark', 'Pre-made floral design bookmark, ready to ship.', 49.00, 100, 'classic-floral.svg', 0),
(1, 'Minimalist Line Art Bookmark', 'Simple, elegant line-art bookmark for everyday reading.', 45.00, 0, 'minimalist-lineart.svg', 0),
(3, 'Reader\'s Gift Set (3-pack)', 'A set of three classic bookmarks, ready for gifting.', 129.00, 25, 'gift-set.svg', 0);
