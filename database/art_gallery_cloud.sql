-- Cloud Database Schema for Aiven, TiDB, PlanetScale, Railway, Supabase
-- This version does NOT execute CREATE DATABASE or USE statements,
-- ensuring safe import directly into your assigned cloud database (e.g. defaultdb).

DROP TABLE IF EXISTS message_replies;
DROP TABLE IF EXISTS messages;
DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS artworks;
DROP TABLE IF EXISTS exhibitions;
DROP TABLE IF EXISTS artists;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(160) NOT NULL UNIQUE,
    phone VARCHAR(30) NULL,
    address VARCHAR(255) NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','customer') NOT NULL DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE artists (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    location VARCHAR(120) NULL,
    bio TEXT NULL,
    image VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE artworks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    artist_id INT NULL,
    title VARCHAR(160) NOT NULL,
    slug VARCHAR(180) NOT NULL UNIQUE,
    category ENUM('Modern','Traditional','Hand Drawn','Abstract','Landscape') NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    size VARCHAR(80) NULL,
    medium VARCHAR(120) NULL,
    image VARCHAR(255) NOT NULL,
    description TEXT NULL,
    status ENUM('Available','Reserved','Sold') NOT NULL DEFAULT 'Available',
    featured TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (artist_id) REFERENCES artists(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE exhibitions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(160) NOT NULL,
    location VARCHAR(160) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    image VARCHAR(255) NULL,
    description TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    order_number VARCHAR(40) NOT NULL UNIQUE,
    total DECIMAL(10,2) NOT NULL DEFAULT 0,
    status ENUM('Pending','Processing','Reserved','Completed','Cancelled') NOT NULL DEFAULT 'Pending',
    customer_note TEXT NULL,
    reference_image VARCHAR(255) NULL,
    delivery_address VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    artwork_id INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (artwork_id) REFERENCES artworks(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(160) NOT NULL,
    subject VARCHAR(180) NOT NULL,
    message TEXT NOT NULL,
    status ENUM('Open','Answered') NOT NULL DEFAULT 'Open',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE message_replies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    message_id INT NOT NULL,
    admin_id INT NOT NULL,
    reply TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (message_id) REFERENCES messages(id) ON DELETE CASCADE,
    FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

INSERT INTO users (name, email, phone, address, password, role) VALUES
('Gallery Admin', 'admin@artgallery.lk', '0700000000', 'Colombo, Sri Lanka', '$2y$12$gWiB95LhnOLAcqn4L0dP4.eqkZSEoon.QylIyOv/QJzWu6GOwvja2', 'admin'),
('Demo Customer', 'customer@artgallery.lk', '0711111111', 'Galle, Sri Lanka', '$2y$12$Yvwkhvy9Im4IgJpJZB7V/Ois/3k9r5xXGg1PwQ39F/WMD2u7sp1Zy', 'customer');

INSERT INTO artists (name, location, bio, image) VALUES
('Ethan Mitchell', 'Venice, Italy', 'Known for layered abstract textures and calm ocean-inspired forms.', 'assets/images/artist-ethan.jpg'),
('Lucas Brown', 'Bangkok, Thailand', 'Creates expressive contemporary work with energetic colour movement.', 'assets/images/artist-lucas.jpg'),
('Oliver Taylor', 'Cape Town, South Africa', 'Works with vivid contrast and bold visual rhythm.', 'assets/images/artist-oliver.jpg'),
('Amara Silva', 'Colombo, Sri Lanka', 'Specialist in hand-drawn and traditional compositions for homes and galleries.', 'assets/images/artist-amara.jpg');

INSERT INTO artworks (artist_id, title, slug, category, price, size, medium, image, description, status, featured) VALUES
(2, 'Whispers of Autumn', 'whispers-of-autumn', 'Modern', 45500.00, '24 x 30 inch', 'Acrylic on canvas', 'assets/images/autumn.jpg', 'A bright modern artwork created with warm autumn colours and bold layered strokes.', 'Available', 1),
(1, 'Floating Worlds', 'floating-worlds', 'Abstract', 62000.00, '30 x 36 inch', 'Mixed media', 'assets/images/floating-worlds.jpg', 'An abstract composition that explores flow, movement, and inner balance.', 'Available', 1),
(1, 'Tranquil Oasis', 'tranquil-oasis', 'Landscape', 78000.00, '32 x 40 inch', 'Oil on canvas', 'assets/images/tranquil-oasis.jpg', 'A peaceful artwork suitable for premium interior spaces and galleries.', 'Available', 1),
(3, 'Midnight Bloom', 'midnight-bloom', 'Abstract', 52500.00, '24 x 24 inch', 'Acrylic ink', 'assets/images/midnight-bloom.jpg', 'A dramatic dark canvas with soft floral movement and premium contrast.', 'Reserved', 0),
(4, 'Golden Memory', 'golden-memory', 'Traditional', 68500.00, '28 x 34 inch', 'Natural pigment', 'assets/images/golden-memory.jpg', 'Traditional inspired artwork with elegant warm tones and cultural richness.', 'Available', 1),
(2, 'Sea of Lines', 'sea-of-lines', 'Hand Drawn', 38000.00, '18 x 24 inch', 'Ink and watercolour', 'assets/images/sea-of-lines.jpg', 'Hand-drawn linework blended with soft colour layers and minimal movement.', 'Available', 0);

INSERT INTO exhibitions (title, location, start_date, end_date, image, description) VALUES
('New Forms in Modern Art', 'Colombo Art Hall', '2026-06-01', '2026-06-10', 'assets/images/exhibition-01.jpg', 'A curated exhibition showcasing modern, traditional, and hand-drawn artworks.'),
('Collectors Private Viewing', 'Galle Creative Studio', '2026-07-05', '2026-07-08', 'assets/images/custom-work.jpg', 'Private viewing event for collectors and gallery customers.');

INSERT INTO messages (user_id, name, email, subject, message, status) VALUES
(2, 'Demo Customer', 'customer@artgallery.lk', 'Custom portrait inquiry', 'I would like to order a custom artwork based on a family reference image.', 'Open');
