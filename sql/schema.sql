-- pondokhuda local dev schema
-- reconstructed from the query surface of api/*.php (2026-08)
-- NOTE: this is a dev skeleton. for prod parity, restore a real mysqldump.
-- collation: utf8mb4_uca1400_ai_ci is mariadb's native utf8mb4 default,
-- so bare connections (SET NAMES utf8mb4) match table collations exactly.

SET NAMES utf8mb4 COLLATE utf8mb4_uca1400_ai_ci;
SET FOREIGN_KEY_CHECKS = 0;

-- ------------------------------------------------ super owner
DROP TABLE IF EXISTS tb_super_owner;
CREATE TABLE tb_super_owner (
  id INT AUTO_INCREMENT PRIMARY KEY,
  kode VARCHAR(20) NOT NULL,
  nama VARCHAR(120) DEFAULT NULL,
  pin VARCHAR(40) DEFAULT NULL,
  email VARCHAR(120) DEFAULT NULL,
  nomor_telepon VARCHAR(30) DEFAULT NULL,
  tanggal_terdaftar DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- ------------------------------------------------ owner
DROP TABLE IF EXISTS tb_owner;
CREATE TABLE tb_owner (
  kode VARCHAR(20) NOT NULL PRIMARY KEY,
  nama VARCHAR(120) DEFAULT NULL,
  pin VARCHAR(40) DEFAULT NULL,
  nomor_telepon VARCHAR(30) DEFAULT NULL,
  email VARCHAR(120) DEFAULT NULL,
  urlfoto VARCHAR(255) DEFAULT NULL,
  tanggal_terdaftar DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- ------------------------------------------------ kost
DROP TABLE IF EXISTS tb_kost;
CREATE TABLE tb_kost (
  kode_kost VARCHAR(20) NOT NULL PRIMARY KEY,
  nama_kost VARCHAR(120) DEFAULT NULL,
  alamat TEXT,
  emailkost VARCHAR(120) DEFAULT NULL,
  primarycolor VARCHAR(20) DEFAULT NULL,
  name_color VARCHAR(20) DEFAULT NULL,
  logo VARCHAR(255) DEFAULT NULL,
  tanggal_terdaftar DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- ------------------------------------------------ owner <-> kost
DROP TABLE IF EXISTS tb_owner_kost;
CREATE TABLE tb_owner_kost (
  id INT AUTO_INCREMENT PRIMARY KEY,
  kode_owner VARCHAR(20) DEFAULT NULL,
  kode_kost VARCHAR(20) DEFAULT NULL
);

-- ------------------------------------------------ admin
DROP TABLE IF EXISTS tb_admin;
CREATE TABLE tb_admin (
  kode VARCHAR(20) NOT NULL PRIMARY KEY,
  nama VARCHAR(120) DEFAULT NULL,
  pin VARCHAR(40) DEFAULT NULL,
  nomor_telepon VARCHAR(30) DEFAULT NULL,
  email VARCHAR(120) DEFAULT NULL,
  urlfoto VARCHAR(255) DEFAULT NULL,
  tanggal_terdaftar DATETIME DEFAULT CURRENT_TIMESTAMP
);

DROP TABLE IF EXISTS tb_admin_kost;
CREATE TABLE tb_admin_kost (
  id INT AUTO_INCREMENT PRIMARY KEY,
  kode_admin VARCHAR(20) DEFAULT NULL,
  kode_kost VARCHAR(20) DEFAULT NULL
);

-- ------------------------------------------------ kamar
DROP TABLE IF EXISTS tb_kamar;
CREATE TABLE tb_kamar (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nokamar VARCHAR(20) DEFAULT NULL,
  statuskamar VARCHAR(30) DEFAULT NULL,
  harga DECIMAL(12,0) DEFAULT 0,
  tanggal_dibuat DATE DEFAULT NULL,
  kode_kost VARCHAR(20) DEFAULT NULL
);

-- ------------------------------------------------ penyewa
DROP TABLE IF EXISTS tb_penyewa;
CREATE TABLE tb_penyewa (
  kode VARCHAR(20) NOT NULL PRIMARY KEY,
  noktp VARCHAR(30) DEFAULT NULL,
  nama VARCHAR(120) DEFAULT NULL,
  tgllahir DATE DEFAULT NULL,
  jk VARCHAR(10) DEFAULT NULL,
  nohp VARCHAR(30) DEFAULT NULL,
  email VARCHAR(120) DEFAULT NULL,
  urlfoto VARCHAR(255) DEFAULT NULL,
  namaortu VARCHAR(120) DEFAULT NULL,
  nohportu VARCHAR(30) DEFAULT NULL,
  alamat TEXT,
  kelurahan VARCHAR(100) DEFAULT NULL,
  kecamatan VARCHAR(100) DEFAULT NULL,
  kotakab VARCHAR(100) DEFAULT NULL,
  provinsi VARCHAR(100) DEFAULT NULL,
  kodepos VARCHAR(10) DEFAULT NULL,
  tempatkuliahkerja VARCHAR(150) DEFAULT NULL,
  jurusankuliah VARCHAR(150) DEFAULT NULL,
  status VARCHAR(30) DEFAULT 'Aktif',
  nomorpin VARCHAR(40) DEFAULT NULL,
  kode_kost VARCHAR(20) DEFAULT NULL,
  tanggal_terdaftar DATE DEFAULT NULL
);

DROP TABLE IF EXISTS tb_penyewa_kamar;
CREATE TABLE tb_penyewa_kamar (
  id INT AUTO_INCREMENT PRIMARY KEY,
  kode_sewa INT DEFAULT NULL,
  kode_penyewa VARCHAR(20) DEFAULT NULL,
  kode_pindah INT DEFAULT NULL
);

-- ------------------------------------------------ sewa
DROP TABLE IF EXISTS tb_sewa_kamar;
CREATE TABLE tb_sewa_kamar (
  kode_sewa INT AUTO_INCREMENT PRIMARY KEY,
  kode_kamar INT DEFAULT NULL,
  tanggal_mulai DATE DEFAULT NULL,
  tanggal_selesai DATE DEFAULT NULL,
  tanggal_pembayaran DATE DEFAULT NULL,
  harga_perbulan DECIMAL(12,0) DEFAULT 0,
  periode_bayar INT DEFAULT 1
);

-- ------------------------------------------------ pembayaran
DROP TABLE IF EXISTS tb_bayar_kost;
CREATE TABLE tb_bayar_kost (
  kode_bayar INT AUTO_INCREMENT PRIMARY KEY,
  kode_sewa INT DEFAULT NULL,
  tanggal_pembayaran DATE DEFAULT NULL,
  tanggal_bayar DATE DEFAULT NULL,
  periode_bayar INT DEFAULT 1,
  harga_perbulan DECIMAL(12,0) DEFAULT 0,
  denda DECIMAL(12,0) DEFAULT 0,
  diskon DECIMAL(12,0) DEFAULT 0,
  harga_pindah DECIMAL(12,0) DEFAULT 0,
  total_harga DECIMAL(14,0) DEFAULT 0,
  total_bayar DECIMAL(14,0) DEFAULT 0,
  total_bayar_sebelumnya DECIMAL(14,0) DEFAULT 0,
  metode VARCHAR(30) DEFAULT NULL
);

-- ------------------------------------------------ pengumuman (berita kost)
DROP TABLE IF EXISTS tb_beritakost;
CREATE TABLE tb_beritakost (
  kode INT AUTO_INCREMENT PRIMARY KEY,
  judul VARCHAR(200) DEFAULT NULL,
  berita TEXT,
  tglpublish DATETIME DEFAULT NULL,
  lastupdate DATETIME DEFAULT NULL,
  status VARCHAR(30) DEFAULT NULL,
  kode_kost VARCHAR(20) DEFAULT NULL
);

DROP TABLE IF EXISTS tb_beritakost_chat;
CREATE TABLE tb_beritakost_chat (
  kode_chat INT AUTO_INCREMENT PRIMARY KEY,
  kode_berita INT DEFAULT NULL,
  kode_user VARCHAR(20) DEFAULT NULL,
  key_user VARCHAR(20) DEFAULT NULL,
  date_time DATETIME DEFAULT NULL,
  message TEXT
);

-- ------------------------------------------------ keluhan
DROP TABLE IF EXISTS tb_keluhan_kategori;
CREATE TABLE tb_keluhan_kategori (
  kode_keluhan INT AUTO_INCREMENT PRIMARY KEY,
  kategori_keluhan VARCHAR(100) DEFAULT NULL
);

DROP TABLE IF EXISTS tb_keluhan_list;
CREATE TABLE tb_keluhan_list (
  id INT AUTO_INCREMENT PRIMARY KEY,
  judul VARCHAR(200) DEFAULT NULL,
  kategori INT DEFAULT NULL,
  uraian TEXT,
  tgl DATETIME DEFAULT NULL,
  user VARCHAR(20) DEFAULT NULL
);

DROP TABLE IF EXISTS tb_keluhan_updatetgl;
CREATE TABLE tb_keluhan_updatetgl (
  id INT AUTO_INCREMENT PRIMARY KEY,
  kode INT DEFAULT NULL,
  status VARCHAR(40) DEFAULT NULL,
  tglupdate DATETIME DEFAULT NULL
);

DROP TABLE IF EXISTS tb_keluhan_chat;
CREATE TABLE tb_keluhan_chat (
  id INT AUTO_INCREMENT PRIMARY KEY,
  kode_user VARCHAR(20) DEFAULT NULL,
  is_admin INT DEFAULT 0,
  date_time DATETIME DEFAULT NULL,
  message TEXT
);

DROP TABLE IF EXISTS tb_keluhan_foto;
CREATE TABLE tb_keluhan_foto (
  id INT AUTO_INCREMENT PRIMARY KEY,
  urlfoto VARCHAR(255) DEFAULT NULL,
  time_created DATETIME DEFAULT NULL
);

DROP TABLE IF EXISTS tb_keluhan_komenadmin;
CREATE TABLE tb_keluhan_komenadmin (
  id INT AUTO_INCREMENT PRIMARY KEY,
  kode_kel INT DEFAULT NULL,
  komen TEXT,
  created DATETIME DEFAULT NULL
);

-- ------------------------------------------------ log
DROP TABLE IF EXISTS tb_log_admin;
CREATE TABLE tb_log_admin (
  id INT AUTO_INCREMENT PRIMARY KEY,
  aktifitas TEXT,
  kode_admin VARCHAR(20) DEFAULT NULL,
  kode_kost VARCHAR(20) DEFAULT NULL,
  tanggal DATETIME DEFAULT CURRENT_TIMESTAMP
);

DROP TABLE IF EXISTS tb_log_owner;
CREATE TABLE tb_log_owner (
  id INT AUTO_INCREMENT PRIMARY KEY,
  aktifitas TEXT,
  kode_owner VARCHAR(20) DEFAULT NULL,
  kode_kost VARCHAR(20) DEFAULT NULL,
  tanggal DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- ------------------------------------------------ keuangan
DROP TABLE IF EXISTS tb_keu_akun;
CREATE TABLE tb_keu_akun (
  kode_akun VARCHAR(20) NOT NULL PRIMARY KEY,
  nama_akun VARCHAR(120) DEFAULT NULL
);

DROP TABLE IF EXISTS tb_keu_jurnal_umum;
CREATE TABLE tb_keu_jurnal_umum (
  id INT AUTO_INCREMENT PRIMARY KEY,
  kode_akun VARCHAR(20) DEFAULT NULL,
  jumlah DECIMAL(14,0) DEFAULT 0,
  saldo DECIMAL(14,0) DEFAULT 0,
  tanggal DATETIME DEFAULT NULL,
  ket VARCHAR(255) DEFAULT NULL
);

DROP TABLE IF EXISTS tb_pendapatan_lainnya;
CREATE TABLE tb_pendapatan_lainnya (
  kode_pendapatan INT AUTO_INCREMENT PRIMARY KEY,
  tanggal DATETIME DEFAULT NULL,
  keterangan TEXT,
  nominal DECIMAL(14,0) DEFAULT 0,
  metode VARCHAR(30) DEFAULT NULL,
  kode_kost VARCHAR(20) DEFAULT NULL
);

DROP TABLE IF EXISTS tb_pengeluaran;
CREATE TABLE tb_pengeluaran (
  kode_pengeluaran INT AUTO_INCREMENT PRIMARY KEY,
  tanggal DATETIME DEFAULT NULL,
  keterangan TEXT,
  nominal DECIMAL(14,0) DEFAULT 0,
  metode VARCHAR(30) DEFAULT NULL,
  kode_kost VARCHAR(20) DEFAULT NULL
);

DROP TABLE IF EXISTS tb_piutang_kost;
CREATE TABLE tb_piutang_kost (
  id INT AUTO_INCREMENT PRIMARY KEY,
  kode_sewa INT DEFAULT NULL,
  jumlah DECIMAL(14,0) DEFAULT 0,
  tanggal DATETIME DEFAULT NULL
);

DROP TABLE IF EXISTS tb_pindah_kamar;
CREATE TABLE tb_pindah_kamar (
  kode_pindah INT AUTO_INCREMENT PRIMARY KEY,
  kode_sewa INT DEFAULT NULL,
  tanggal_pindah DATETIME DEFAULT NULL
);

-- ------------------------------------------------ wilayah
DROP TABLE IF EXISTS tb_master_prov;
CREATE TABLE tb_master_prov (
  id INT AUTO_INCREMENT PRIMARY KEY,
  provinsi VARCHAR(100) DEFAULT NULL,
  kotakab VARCHAR(100) DEFAULT NULL,
  kecamatan VARCHAR(100) DEFAULT NULL,
  kodepos VARCHAR(10) DEFAULT NULL
);

-- ------------------------------------------------ status bayar view
DROP VIEW IF EXISTS tbv_status_bayar;
CREATE VIEW tbv_status_bayar AS
SELECT
  b.kode_sewa AS kode_sewa,
  b.tanggal_bayar AS tanggal_bayar_sebelumnya,
  b.tanggal_pembayaran AS tanggal_pembayaran_sebelumnya,
  DATE_ADD(b.tanggal_pembayaran, INTERVAL b.periode_bayar MONTH) AS tanggal_pembayaran_selanjutnya,
  DATEDIFF(DATE_ADD(b.tanggal_pembayaran, INTERVAL b.periode_bayar MONTH), CURDATE()) AS hari_menuju_bayar,
  IF((b.total_harga - (b.total_bayar + b.total_bayar_sebelumnya)) <= 0,
     CONVERT(_utf8mb4'Lunas' USING utf8mb4) COLLATE utf8mb4_uca1400_ai_ci,
     CONVERT(_utf8mb4'Belum Lunas' USING utf8mb4) COLLATE utf8mb4_uca1400_ai_ci) AS status_bayar,
  b.total_harga AS total_harga_sebelumnya,
  (b.total_harga - (b.total_bayar + b.total_bayar_sebelumnya)) AS sisa_bayar_sebelumnya,
  b.total_bayar AS total_bayar_sebelumnya,
  DATE_ADD(b.tanggal_pembayaran, INTERVAL 33 DAY) AS tanggal_alarm,
  b.periode_bayar AS periode_bayar_sebelumnya,
  b.harga_perbulan AS harga_perbulan,
  b.denda AS denda_sebelumnya,
  b.diskon AS diskon_sebelumnya,
  b.harga_pindah AS harga_pindah_sebelumnya,
  b.denda AS denda
FROM tb_bayar_kost b
JOIN (SELECT kode_sewa, MAX(kode_bayar) AS m FROM tb_bayar_kost GROUP BY kode_sewa) x
  ON x.kode_sewa = b.kode_sewa AND x.m = b.kode_bayar;

SET FOREIGN_KEY_CHECKS = 1;