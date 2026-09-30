-- =========================================
-- EVENT TICKETING SYSTEM
-- DATABASE: dbticketevent
-- =========================================

-- Hapus tabel jika sebelumnya sudah ada
DROP TABLE IF EXISTS payments CASCADE;
DROP TABLE IF EXISTS order_items CASCADE;
DROP TABLE IF EXISTS orders CASCADE;
DROP TABLE IF EXISTS tickets CASCADE;
DROP TABLE IF EXISTS events CASCADE;
DROP TABLE IF EXISTS categories CASCADE;
DROP TABLE IF EXISTS users CASCADE;


-- =========================================
-- 1. USERS
-- =========================================

CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(30) NOT NULL DEFAULT 'admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


-- =========================================
-- 2. CATEGORIES
-- =========================================

CREATE TABLE categories (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


-- =========================================
-- 3. EVENTS
-- =========================================

CREATE TABLE events (
    id SERIAL PRIMARY KEY,
    category_id INT NOT NULL,

    name VARCHAR(150) NOT NULL,
    description TEXT,

    location VARCHAR(255) NOT NULL,

    event_date DATE NOT NULL,
    event_time TIME NOT NULL,

    image VARCHAR(255),

    status VARCHAR(30) NOT NULL DEFAULT 'upcoming',

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_events_category
        FOREIGN KEY (category_id)
        REFERENCES categories(id),

    CONSTRAINT check_event_status
        CHECK (
            status IN (
                'upcoming',
                'ongoing',
                'completed',
                'cancelled'
            )
        )
);


-- =========================================
-- 4. TICKETS
-- =========================================

CREATE TABLE tickets (
    id SERIAL PRIMARY KEY,

    event_id INT NOT NULL,

    name VARCHAR(100) NOT NULL,

    price DECIMAL(12,2) NOT NULL,

    stock INT NOT NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_tickets_event
        FOREIGN KEY (event_id)
        REFERENCES events(id),

    CONSTRAINT check_ticket_price
        CHECK (price >= 0),

    CONSTRAINT check_ticket_stock
        CHECK (stock >= 0)
);


-- =========================================
-- 5. ORDERS
-- =========================================

CREATE TABLE orders (
    id SERIAL PRIMARY KEY,

    order_code VARCHAR(50) UNIQUE NOT NULL,

    customer_name VARCHAR(100) NOT NULL,

    customer_email VARCHAR(150) NOT NULL,

    customer_phone VARCHAR(30) NOT NULL,

    total_amount DECIMAL(12,2) NOT NULL,

    status VARCHAR(30) NOT NULL DEFAULT 'pending',

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT check_order_total
        CHECK (total_amount >= 0),

    CONSTRAINT check_order_status
        CHECK (
            status IN (
                'pending',
                'paid',
                'cancelled'
            )
        )
);


-- =========================================
-- 6. ORDER ITEMS
-- =========================================

CREATE TABLE order_items (
    id SERIAL PRIMARY KEY,

    order_id INT NOT NULL,

    ticket_id INT NOT NULL,

    quantity INT NOT NULL,

    price DECIMAL(12,2) NOT NULL,

    subtotal DECIMAL(12,2) NOT NULL,

    CONSTRAINT fk_order_items_order
        FOREIGN KEY (order_id)
        REFERENCES orders(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_order_items_ticket
        FOREIGN KEY (ticket_id)
        REFERENCES tickets(id),

    CONSTRAINT check_order_item_quantity
        CHECK (quantity > 0),

    CONSTRAINT check_order_item_price
        CHECK (price >= 0),

    CONSTRAINT check_order_item_subtotal
        CHECK (subtotal >= 0)
);


-- =========================================
-- 7. PAYMENTS
-- =========================================

CREATE TABLE payments (
    id SERIAL PRIMARY KEY,

    order_id INT NOT NULL UNIQUE,

    payment_method VARCHAR(30) NOT NULL,

    amount DECIMAL(12,2) NOT NULL,

    status VARCHAR(30) NOT NULL DEFAULT 'pending',

    paid_at TIMESTAMP,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_payments_order
        FOREIGN KEY (order_id)
        REFERENCES orders(id)
        ON DELETE CASCADE,

    CONSTRAINT check_payment_method
        CHECK (
            payment_method IN (
                'transfer_bank',
                'e_wallet',
                'qris'
            )
        ),

    CONSTRAINT check_payment_amount
        CHECK (amount >= 0),

    CONSTRAINT check_payment_status
        CHECK (
            status IN (
                'pending',
                'paid',
                'failed'
            )
        )
);


-- =========================================
-- DUMMY DATA
-- =========================================


-- ADMIN

INSERT INTO users (
    name,
    email,
    password,
    role
)
VALUES (
    'Administrator',
    'admin@gmail.com',
    'password',
    'admin'
);


-- CATEGORY

INSERT INTO categories (
    name,
    description
)
VALUES (
    'Music Festival',
    'Festival musik'
);


-- EVENT

INSERT INTO events (
    category_id,
    name,
    description,
    location,
    event_date,
    event_time,
    image,
    status
)
VALUES (
    1,
    'Java Music Festival',
    'Festival musik selama dua hari',
    'Surabaya',
    '2026-11-15',
    '15:00:00',
    NULL,
    'upcoming'
);


-- TICKETS

INSERT INTO tickets (
    event_id,
    name,
    price,
    stock
)
VALUES
(
    1,
    'ONE DAY PASS - DAY 1',
    135000,
    100
),
(
    1,
    'ONE DAY PASS - DAY 2',
    135000,
    100
),
(
    1,
    'VIP PASS',
    500000,
    50
);