-- mkfilms.studio Database Schema
-- Run this on your MySQL/MariaDB

CREATE DATABASE IF NOT EXISTS mkfilms CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE mkfilms;

-- Users (Google OAuth only)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    google_id VARCHAR(255) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    avatar VARCHAR(500),
    bio TEXT,
    location VARCHAR(255) DEFAULT 'Himachal Pradesh',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Weddings
CREATE TABLE weddings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    couple_names VARCHAR(255) NOT NULL,
    wedding_date DATE,
    location VARCHAR(255),
    description TEXT,
    cover_photo VARCHAR(500),
    is_published TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Photos
CREATE TABLE photos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    wedding_id INT NOT NULL,
    filename VARCHAR(500) NOT NULL,
    caption TEXT,
    like_count INT DEFAULT 0,
    is_featured TINYINT(1) DEFAULT 0,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (wedding_id) REFERENCES weddings(id) ON DELETE CASCADE
);

-- Reels
CREATE TABLE reels (
    id INT AUTO_INCREMENT PRIMARY KEY,
    wedding_id INT NOT NULL,
    title VARCHAR(255),
    filename VARCHAR(500) NOT NULL,
    thumbnail VARCHAR(500),
    view_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (wedding_id) REFERENCES weddings(id) ON DELETE CASCADE
);

-- Likes (users who liked a photo)
CREATE TABLE photo_likes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    photo_id INT NOT NULL,
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_like (photo_id, user_id),
    FOREIGN KEY (photo_id) REFERENCES photos(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Comments
CREATE TABLE comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    photo_id INT NOT NULL,
    user_id INT NOT NULL,
    comment TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (photo_id) REFERENCES photos(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Pre-booking enquiries
CREATE TABLE prebookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(255),
    wedding_date DATE,
    message TEXT,
    source VARCHAR(50) DEFAULT 'popup',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Seed: 1 wedding
INSERT INTO weddings (title, couple_names, wedding_date, location, description) VALUES
('Arjun & Priya — A Himachali Love Story', 'Arjun & Priya', '2025-11-15', 'Mandi, Himachal Pradesh', 'A beautiful traditional Himachali wedding set against the backdrop of the Beas river valley. Every moment captured with love.');

-- Seed: 50 demo photos (replace filenames with your real uploads)
INSERT INTO photos (wedding_id, filename, caption, like_count, is_featured) VALUES
(1, 'photo_01.jpg', 'The first look 💫', 142, 1),
(1, 'photo_02.jpg', 'Baraat vibes 🎺', 98, 1),
(1, 'photo_03.jpg', 'Varmala ceremony', 201, 1),
(1, 'photo_04.jpg', 'Haldi glow ✨', 87, 0),
(1, 'photo_05.jpg', 'Mehendi magic', 134, 1),
(1, 'photo_06.jpg', 'Pheras', 176, 1),
(1, 'photo_07.jpg', 'Couple portrait at sunset', 220, 1),
(1, 'photo_08.jpg', 'Family blessings', 65, 0),
(1, 'photo_09.jpg', 'The ring moment 💍', 189, 1),
(1, 'photo_10.jpg', 'Bridal details', 112, 0),
(1, 'photo_11.jpg', 'Lehenga twirl', 156, 1),
(1, 'photo_12.jpg', 'Groom getting ready', 78, 0),
(1, 'photo_13.jpg', 'Bride & her squad', 143, 1),
(1, 'photo_14.jpg', 'Mandap decor', 94, 0),
(1, 'photo_15.jpg', 'Mountains & love', 267, 1),
(1, 'photo_16.jpg', 'Night portrait', 198, 1),
(1, 'photo_17.jpg', 'Candid laugh', 88, 0),
(1, 'photo_18.jpg', 'Traditional attire', 121, 1),
(1, 'photo_19.jpg', 'Sacred fire', 76, 0),
(1, 'photo_20.jpg', 'Vidaai emotions', 234, 1),
(1, 'photo_21.jpg', 'Couple walk', 109, 0),
(1, 'photo_22.jpg', 'Temple backdrop', 155, 1),
(1, 'photo_23.jpg', 'Bridal glow', 178, 1),
(1, 'photo_24.jpg', 'Himachali folk dance', 167, 0),
(1, 'photo_25.jpg', 'Reception look', 203, 1),
(1, 'photo_26.jpg', 'Fairy lights & love', 145, 1),
(1, 'photo_27.jpg', 'Mom & daughter', 189, 1),
(1, 'photo_28.jpg', 'Dad & son', 172, 0),
(1, 'photo_29.jpg', 'Couple selfie', 231, 1),
(1, 'photo_30.jpg', 'Sangeet night', 118, 0),
(1, 'photo_31.jpg', 'Dinner setup', 67, 0),
(1, 'photo_32.jpg', 'Dupatta portrait', 194, 1),
(1, 'photo_33.jpg', 'Golden hour magic', 256, 1),
(1, 'photo_34.jpg', 'Hands together', 143, 0),
(1, 'photo_35.jpg', 'Beas river backdrop', 211, 1),
(1, 'photo_36.jpg', 'Bride\'s smile', 187, 1),
(1, 'photo_37.jpg', 'Groom portrait', 134, 0),
(1, 'photo_38.jpg', 'Flower shower', 165, 1),
(1, 'photo_39.jpg', 'Late night dance', 89, 0),
(1, 'photo_40.jpg', 'Sisters together', 176, 1),
(1, 'photo_41.jpg', 'Mountain frame', 244, 1),
(1, 'photo_42.jpg', 'Intimate moment', 198, 1),
(1, 'photo_43.jpg', 'Doli farewell', 223, 1),
(1, 'photo_44.jpg', 'Anklet detail', 97, 0),
(1, 'photo_45.jpg', 'Jaimala smiles', 167, 1),
(1, 'photo_46.jpg', 'Candid tears', 201, 1),
(1, 'photo_47.jpg', 'Kids at wedding', 78, 0),
(1, 'photo_48.jpg', 'Couple in car', 154, 1),
(1, 'photo_49.jpg', 'Starry night portrait', 289, 1),
(1, 'photo_50.jpg', 'Forever starts here', 312, 1);

-- Seed: 3 reels
INSERT INTO reels (wedding_id, title, filename, thumbnail) VALUES
(1, 'Highlight Reel — Arjun & Priya', 'reel_highlight.mp4', 'reel_01_thumb.jpg'),
(1, 'Haldi & Mehendi Ceremony', 'reel_haldi.mp4', 'reel_02_thumb.jpg'),
(1, 'The Wedding Day — Full Story', 'reel_fullday.mp4', 'reel_03_thumb.jpg');
