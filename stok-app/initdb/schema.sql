-- ======================
-- DATABASE UNTUK LOGIN
-- ======================
CREATE DATABASE IF NOT EXISTS stok_auth;
USE stok_auth;
 
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) NOT NULL,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin', 'staff') DEFAULT 'staff',
  divisi ENUM('moldshop', 'maintenance') NOT NULL DEFAULT 'moldshop'
);

-- ======================
-- DATABASE UNTUK MOLD SHOP
-- ======================
CREATE DATABASE IF NOT EXISTS stok_moldshop;
USE stok_moldshop;

CREATE TABLE items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama_barang VARCHAR(150) NOT NULL,
  model VARCHAR(100),
  stok_awal INT DEFAULT 0,
  barang_masuk INT DEFAULT 0,
  barang_keluar INT DEFAULT 0,
  sisa_stok INT GENERATED ALWAYS AS (stok_awal + barang_masuk - barang_keluar) STORED,
  tanggal_update DATE DEFAULT (CURRENT_DATE)
);

CREATE TABLE stock_movements (
  id INT AUTO_INCREMENT PRIMARY KEY,
  item_id INT,
  tanggal DATE,
  stok_awal INT,
  barang_masuk INT,
  barang_keluar INT,
  keterangan VARCHAR(255),
  FOREIGN KEY (item_id) REFERENCES items(id)
);

CREATE TABLE IF NOT EXISTS rekap_bulanan (
  id INT AUTO_INCREMENT PRIMARY KEY,
  bulan INT,
  tahun INT,
  periode VARCHAR(7), -- contoh: 2025-10
  item_id INT,
  nama_barang VARCHAR(150),
  stok_awal INT,
  barang_masuk INT,
  barang_keluar INT,
  stok_akhir INT,
  tanggal_generate DATE DEFAULT (CURRENT_DATE),
  FOREIGN KEY (item_id) REFERENCES items(id)
);

CREATE TABLE IF NOT EXISTS setting_stok (
  id INT AUTO_INCREMENT PRIMARY KEY,
  batas_minimum INT NOT NULL DEFAULT 5
);

-- Isi nilai awal 
INSERT INTO setting_stok (batas_minimum) VALUES (5);

-- ======================
-- DATABASE UNTUK MAINTENANCE
-- ======================
CREATE DATABASE IF NOT EXISTS stok_maintenance;
USE stok_maintenance;

CREATE TABLE items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama_barang VARCHAR(150) NOT NULL,
  model VARCHAR(100),
  stok_awal INT DEFAULT 0,
  barang_masuk INT DEFAULT 0,
  barang_keluar INT DEFAULT 0,
  sisa_stok INT GENERATED ALWAYS AS (stok_awal + barang_masuk - barang_keluar) STORED,
  tanggal_update DATE DEFAULT (CURRENT_DATE)
);

CREATE TABLE stock_movements (
  id INT AUTO_INCREMENT PRIMARY KEY,
  item_id INT,
  tanggal DATE,
  stok_awal INT,
  barang_masuk INT,
  barang_keluar INT,
  keterangan VARCHAR(255),
  FOREIGN KEY (item_id) REFERENCES items(id)
);

CREATE TABLE IF NOT EXISTS rekap_bulanan (
  id INT AUTO_INCREMENT PRIMARY KEY,
  bulan INT,
  tahun INT,
  periode VARCHAR(7), -- contoh: 2025-10
  item_id INT,
  nama_barang VARCHAR(150),
  stok_awal INT,
  barang_masuk INT,
  barang_keluar INT,
  stok_akhir INT,
  tanggal_generate DATE DEFAULT (CURRENT_DATE),
  FOREIGN KEY (item_id) REFERENCES items(id)
);

CREATE TABLE IF NOT EXISTS setting_stok (
  id INT AUTO_INCREMENT PRIMARY KEY,
  batas_minimum INT NOT NULL DEFAULT 5
);

-- Isi nilai awal 
INSERT INTO setting_stok (batas_minimum) VALUES (5);
