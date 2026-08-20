-- pondokhuda local dev seed data
-- login credentials for the four roles:
--   super owner : kode=so001  pin=123456
--   owner       : kode=o001w  pin=123456
--   admin       : kode=a001d  pin=123456
--   penyewa     : kode=pa0001 pin=123456

USE pondokhuda_dev;

INSERT INTO tb_super_owner (kode, nama, pin, email) VALUES
('so001', 'Super Owner Dev', '123456', 'super@dev.local');

INSERT INTO tb_owner (kode, nama, pin, nomor_telepon, email) VALUES
('o001w', 'Owner Dev', '123456', '081200000001', 'owner@dev.local');

INSERT INTO tb_kost (kode_kost, nama_kost, alamat, emailkost, primarycolor, name_color, logo)
VALUES ('K001', 'Kost Pondok Huda Dev', 'Jl. Contoh No. 1, Kota', 'kost@dev.local', '#1E88E5', '#1565C0', '/Assets/images/logo/logo.png');

INSERT INTO tb_owner_kost (kode_owner, kode_kost) VALUES ('o001w', 'K001');

INSERT INTO tb_admin (kode, kode_owner, nama, pin, nomor_telepon, email) VALUES
('a001d', 'o001w', 'Admin Dev', '123456', '081200000002', 'admin@dev.local');

INSERT INTO tb_admin_kost (kode_admin, kode_kost) VALUES ('a001d', 'K001');

INSERT INTO tb_kamar (id, nokamar, statuskamar, harga, tanggal_dibuat, kode_kost) VALUES
(1, 'A1', 'Terisi', 500000, '2026-01-01', 'K001'),
(2, 'A2', 'Kosong', 500000, '2026-01-01', 'K001');

INSERT INTO tb_penyewa (kode, noktp, nama, tgllahir, jk, nohp, email, namaortu, nohportu, alamat,
  kelurahan, kecamatan, kotakab, provinsi, kodepos, tempatkuliahkerja, jurusankuliah, status, nomorpin, kode_kost, tanggal_terdaftar)
VALUES ('pa0001', '3171000000000001', 'Penyewa Dev', '2000-05-10', 'L', '081200000003', 'penyewa@dev.local',
  'Orang Tua Dev', '081200000004', 'Jl. Rumah No. 2', 'Kelurahan', 'Kecamatan', 'Kota', 'DKI Jakarta',
  '123456', 'Kampus Dev', 'Teknik', 'Aktif', '12345', 'K001', '2026-01-15');

INSERT INTO tb_sewa_kamar (kode_sewa, kode_kamar, tanggal_mulai, tanggal_selesai, tanggal_pembayaran, harga_perbulan, periode_bayar)
VALUES (1, 1, '2026-01-15', NULL, '2026-02-15', 500000, 1);

INSERT INTO tb_penyewa_kamar (kode_sewa, kode_penyewa) VALUES (1, 'pa0001');

INSERT INTO tb_bayar_kost (kode_bayar, kode_sewa, tanggal_pembayaran, tanggal_bayar, periode_bayar, harga_perbulan,
  denda, diskon, harga_pindah, total_harga, total_bayar, total_bayar_sebelumnya, metode)
VALUES (1, 1, '2026-02-15', '2026-02-14', 1, 500000, 0, 0, 0, 500000, 500000, 0, 'Transfer');

INSERT INTO tb_beritakost (judul, berita, tglpublish, lastupdate, status, kode_kost) VALUES
('Pengumuman Awal Tahun', 'Selamat datang di masa sewa baru.', NOW(), NOW(), 'tampil', 'K001');

INSERT INTO tb_keluhan_kategori (kode_keluhan, kategori_keluhan) VALUES
(1, 'Kerusakan Fasilitas'), (2, 'Kebersihan'), (3, 'Lainnya');

INSERT INTO tb_keu_akun (kode_akun, nama_akun, kode_jenis_akun) VALUES
('1.1.01', 'Kas', 1),
('4.1.01', 'Pendapatan Sewa', 3),
('5.1.01', 'Beban Operasional', 4);

INSERT INTO tb_master_prov (provinsi, kotakab, kecamatan, kodepos) VALUES
('DKI Jakarta', 'Jakarta Selatan', 'Kebayoran Baru', '12110'),
('Jawa Barat', 'Bandung', 'Coblong', '40132');
