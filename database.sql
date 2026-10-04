-- =====================================================
-- Database: Daun Hijau - Toko Tanaman Hias
-- Import lewat phpMyAdmin atau: mysql -u root -p < database.sql
-- =====================================================
CREATE DATABASE IF NOT EXISTS toko_tanaman
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE toko_tanaman;

DROP TABLE IF EXISTS pesanan;
DROP TABLE IF EXISTS tanaman;
DROP TABLE IF EXISTS users;

-- Tabel pengguna (admin/penjual dan pembeli)
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(100) NOT NULL,
  email VARCHAR(120) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin','pembeli') NOT NULL DEFAULT 'pembeli',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Tabel katalog tanaman
CREATE TABLE tanaman (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(100) NOT NULL,
  nama_latin VARCHAR(120) DEFAULT NULL,
  emoji VARCHAR(8) NOT NULL DEFAULT '🌿',
  kebutuhan_cahaya ENUM('teduh','terang') NOT NULL DEFAULT 'teduh',
  tingkat_perawatan ENUM('mudah','sedang') NOT NULL DEFAULT 'mudah',
  harga INT NOT NULL DEFAULT 0,
  stok INT NOT NULL DEFAULT 0,
  jadwal_siram INT NOT NULL DEFAULT 3 COMMENT 'disiram setiap N hari',
  deskripsi TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Tabel pesanan
CREATE TABLE pesanan (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  tanaman_id INT DEFAULT NULL,
  nama_tanaman VARCHAR(100) NOT NULL,
  jumlah INT NOT NULL DEFAULT 1,
  total INT NOT NULL DEFAULT 0,
  alamat TEXT NOT NULL,
  catatan VARCHAR(255) DEFAULT NULL,
  status ENUM('baru','diproses','dikirim','selesai') NOT NULL DEFAULT 'baru',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (tanaman_id) REFERENCES tanaman(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Data contoh tanaman
INSERT INTO tanaman (nama, nama_latin, emoji, kebutuhan_cahaya, tingkat_perawatan, harga, stok, jadwal_siram, deskripsi) VALUES
('Lidah Mertua', 'Sansevieria trifasciata', '🪴', 'teduh', 'mudah', 75000, 20, 14, 'Sangat tahan banting, cocok untuk pemula dan kamar kos yang minim cahaya.'),
('Sirih Gading', 'Epipremnum aureum', '🍃', 'teduh', 'mudah', 45000, 30, 5, 'Rambatnya cantik untuk digantung. Tumbuh cepat dan mudah diperbanyak.'),
('ZZ Plant', 'Zamioculcas zamiifolia', '🌱', 'teduh', 'mudah', 120000, 12, 14, 'Daun mengilap, hanya perlu disiram dua minggu sekali.'),
('Calathea Orbifolia', 'Goeppertia orbifolia', '🍀', 'teduh', 'sedang', 95000, 8, 4, 'Daun bulat bergaris perak. Suka lembap dan tidak tahan matahari langsung.'),
('Aglaonema Merah', 'Aglaonema sp.', '🌺', 'teduh', 'mudah', 85000, 15, 7, 'Daun kemerahan yang cerah, tetap sehat di dalam ruangan.'),
('Monstera', 'Monstera deliciosa', '🌿', 'terang', 'mudah', 150000, 10, 7, 'Daun berlubang yang ikonik. Taruh dekat jendela dengan cahaya terang.'),
('Kaktus Mini', 'Mammillaria sp.', '🌵', 'terang', 'mudah', 25000, 40, 14, 'Mungil, murah, dan hampir tidak perlu air. Suka sinar matahari.'),
('Lidah Buaya', 'Aloe vera', '🌿', 'terang', 'mudah', 35000, 18, 10, 'Bermanfaat untuk perawatan kulit. Letakkan di tempat terang dan jangan terlalu sering disiram.');

-- Akun demo (admin & pembeli) dibuat otomatis oleh setup.php
-- agar password tersimpan dengan hash yang aman.
