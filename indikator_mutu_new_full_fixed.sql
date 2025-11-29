-- MySQL-compatible dump (converted)
DROP DATABASE IF EXISTS indikator_mutu;
CREATE DATABASE indikator_mutu;
USE indikator_mutu;


-- 1. Ruangan (induk dari user, bio_pasien, mutu_ruangan)
CREATE TABLE ruangan (
    id_ruangan VARCHAR(255) PRIMARY KEY,
    nama_ruangan VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Kategori 
CREATE TABLE kategori(
    id_kategori INT AUTO_INCREMENT PRIMARY KEY,
    kategori VARCHAR (100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Indikator Mutu (induk dari mutu_ruangan)
CREATE TABLE indikator_mutu(
    id_indikator INT AUTO_INCREMENT PRIMARY KEY,
	id_kategori INT NOT NULL,
    variabel TEXT NOT NULL,
    standar TEXT NOT NULL,
    CONSTRAINT fk_indikator_kategori FOREIGN KEY (id_kategori) REFERENCES kategori(id_kategori)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Indikator Ruangan (Inputan Superadmin)
CREATE TABLE indikator_ruangan(
    id_indikator_ruangan INT AUTO_INCREMENT PRIMARY KEY,
	id_ruangan VARCHAR(255) NOT NULL,
    id_indikator INT NOT NULL,
	active BOOL NOT NULL,
    CONSTRAINT fk_indikator_ruangan FOREIGN KEY (id_ruangan) REFERENCES ruangan(id_ruangan),
    CONSTRAINT fk_indikator_indikator FOREIGN KEY (id_indikator) REFERENCES indikator_mutu(id_indikator)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE pertanyaan (
    id_pertanyaan INT AUTO_INCREMENT PRIMARY KEY,
    pertanyaan VARCHAR(255) NOT NULL,
    urutan INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. User (punya FK ke ruangan)
CREATE TABLE `user` (
    id_user VARCHAR(255) PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    id_ruangan VARCHAR(255) NOT NULL,
	nama_ruangan TEXT NOT NULL,
    CONSTRAINT fk_user_ruangan FOREIGN KEY (id_ruangan) REFERENCES ruangan(id_ruangan)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. Bio Pasien (punya FK ke ruangan)
CREATE TABLE bio_pasien (
    id_pasien INT AUTO_INCREMENT PRIMARY KEY,
    id_ruangan VARCHAR(255) NOT NULL,
    no_rm VARCHAR(100) NOT NULL,
    umur INT NOT NULL,bio_pasien
    jenis_kelamin VARCHAR(50) NOT NULL,
    pendidikan VARCHAR(50) NOT NULL,
    pekerjaan VARCHAR(100) NOT NULL,
    CONSTRAINT fk_pasien_ruangan FOREIGN KEY (id_ruangan) REFERENCES ruangan(id_ruangan)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6. Pilihan Jawaban (punya FK ke pertanyaan)
CREATE TABLE pilihan_jawaban (
    id_pilihan INT AUTO_INCREMENT PRIMARY KEY,
    id_pertanyaan INT NOT NULL,
    pilihan VARCHAR(255) NOT NULL,
    nilai INT NOT NULL,
    CONSTRAINT fk_pilihan_pertanyaan FOREIGN KEY (id_pertanyaan) REFERENCES pertanyaan(id_pertanyaan)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 7. Jawaban (punya FK ke pertanyaan & pilihan_jawaban)
CREATE TABLE jawaban (
    id_jawaban INT AUTO_INCREMENT PRIMARY KEY,
    tanggal DATE NOT NULL,
	id_pasien INT NOT NULL,
    id_pertanyaan INT NOT NULL,
    id_pilihan INT,
    hasil_nilai TEXT NOT NULL,
    CONSTRAINT fk_jawaban_pertanyaan FOREIGN KEY (id_pertanyaan) REFERENCES pertanyaan(id_pertanyaan),
    CONSTRAINT fk_jawaban_pilihan FOREIGN KEY (id_pilihan) REFERENCES pilihan_jawaban(id_pilihan),
	CONSTRAINT fk_id_pasien FOREIGN KEY (id_pasien) REFERENCES bio_pasien(id_pasien)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- 8. Mutu Ruangan (punya FK ke ruangan & indikator_mutu)
CREATE TABLE mutu_ruangan (
    id_mutu INT AUTO_INCREMENT PRIMARY KEY,
    tanggal DATE NOT NULL, 
    id_indikator_ruangan INT NOT NULL, 
    total_pasien INT NOT NULL, 
    pasien_sesuai INT NOT NULL,
    CONSTRAINT fk_mutu_indikator FOREIGN KEY (id_indikator_ruangan) REFERENCES indikator_ruangan(id_indikator_ruangan)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 9. Sessions (Wajib untuk Laravel)
DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- INSERT INTO

-- ruangan
INSERT INTO ruangan (id_ruangan, nama_ruangan) VALUES
('SP00', 'Super Admin'),
('R01', 'Nifas'),
('R02', 'Perinatologi'),
('R03', 'VK'),
('R04', 'Farmasi'),
('R05', 'Laboratorium'),
('R06', 'Anak'),
('R07', 'CSSD'),
('R08', 'Interna'),
('R09', 'IBS'),
('R10', 'ICU'),
('R11', 'IGD'),
('R12', 'IPSRS'),
('R13', 'Laundry'),
('R14', 'Bedah'),
('R15', 'Gizi'),
('R16', 'Rajal'),
('R17', 'VIP'),
('R18', 'RM'),
('R19', 'Radiologi'),
('R20', 'Keuangan'),
('R21', 'Kantor');

-- kategori indikator
INSERT INTO kategori (kategori) VALUES
('Indikator Mutu Prioritas Unit'),
('Indikator Nasional Mutu'),
('Indikator Mutu Prioritas RS');

-- indikator mutu
INSERT INTO indikator_mutu (id_kategori, variabel, standar) VALUES
-- Indikator Mutu Prioritas Unit (IMPU)
(1, 'Kejadian retensi urin pada pasien post partum', '≤2'),
(1, 'Ketepatan skrining kegawatan distress pernafasan pada bayi baru lahir', '100'),
(1, 'Kepatuhan bidan mengisi buku KIA secara lengkap',  '100'),
(1, 'Ketersediaan stok obat dan alat kesehatan di gudang farmasi pada saat defekta', '100'),
(1, 'Angka kegagalan pengambilan sampel darah pada bayi (0-28 hari)', '<5'),
(1, 'Ketepatan pengisian resume medis pasien pulang', '100'),
(1, 'Waktu proses sterilisasi alat', '100'),
(1, 'Kepatuhan penginputan pasien susp TB ke dalam aplikasi SITB', '100'),
(1, 'Kepatuhan pelaksanaan sign in sign out', '100'),
(1, 'Angka kejadian pasien menolak intubasi', '≤2'),
(1, 'Ketepatan pemasangan infus pada pasien anak dan bayi', '100'),
(1, 'Angka pemeliharaan instalasi pengolahan air limbah', '100'),
(1, 'Tidak ada linen/baju dicuci yang hilang', '100'),
(1, 'Waktu mobilisasi pertama pasien pasca bedah', '100'),
(1, 'Ketepatan waktu pengambilan alat makan pasien rawat inap', '100'),
(1, 'Kepatuhan petugas dalam edukasi pasien tentang pendaftaran online', '100'),
(1, 'Kecepatan waktu tanggap terhadap panggilan Ners call', '100'),
(1, 'Mulai waktu tunggu admisi (TASK ID 1)', '-'),
(1, 'Kepatuhan pemberian label pada hasil radiologi', '100'),
(1, 'Kesesuaian jumlah pendapatan pada SIMRS dengan kas bank', '100'),

-- Indikator Nasional Mutu (INM)
(2, 'Kepatuhan Cuci tangan', '85'),
(2, 'Kepatuhan Penggunaan APD', '100'),
(2, 'Identifikasi Pasien', '100'),
(2, 'Ketepatan Waktu Visite Dokter', '80'),
(2, 'Kepatuhan Terhadap Clinical Pathway',  '80'),
(2, 'Kepatuhan Upaya Pencegahan Resiko Jatuh', '100'),
(2, 'Tanggap Komplain','80'),
(2, 'Waktu Tanggap SC', '80'),
(2, 'Penundaan Operasi Elektif', '5'),
(2, 'Penggunaan Obat Sesuai Formularium Nasional',  '80'),
(2, 'Pelaporan hasil Laboratorium Kritis', '100'),
(2, 'Waktu tunggu Rawat Jalan', '80'),

-- Indikator Mutu Prioritas RS (IMPRS)
(3, 'Ketepatan identifikasi pasien', '100'),
(3, 'Kepatuhan perawat melakukan pelaporan menggunakan format SBAR dan read back', '100'),
(3, 'Kepatuhan dokter dalam pemberian site marking', '100'),
(3, 'Kepatuhan cuci tangan', '85'),
(3, 'Kepatuhan upaya pencegahan resiko jatuh', '100'),
(3, 'Waktu respon penanganan gangguan sistem sysmed RS', '100'),
(3, 'Ketepatan serah terima pasien post op dengan perawat recovery room', '100'),
(3, 'Kelengkapan sarana prasarana di Ruang NICU', '100'),
(3, 'Kepatuhan dalam upaya meningkatkan keamanan obat yang perlu diwaspadai (High alert)', '100'),
(3, 'Kepatuhan upaya pencegahan kegagalan supply air bersih rumah sakit', '100');


-- indikator ruangan (Superadmin)
INSERT INTO indikator_ruangan (id_ruangan, id_indikator, active) VALUES
('R01', 1, TRUE),  -- Kejadian retensi urin pada pasien post partum
('R01', 21, TRUE), -- Kepatuhan Cuci tangan
('R01', 22, TRUE), -- Kepatuhan Penggunaan APD
('R01', 23, TRUE), -- Identifikasi Pasien
('R01', 24, TRUE), -- Ketepatan Waktu Visite Dokter
('R01', 25, TRUE), -- Kepatuhan Terhadap Clinical Pathway
('R01', 26, TRUE), -- Kepatuhan Upaya Pencegahan Resiko Jatuh
('R01', 27, TRUE), -- Tanggap Komplain
('R01', 28, TRUE), -- Kepuasan Masyarakat
('R01', 34, TRUE), -- Ketepatan identifikasi pasien
('R01', 35, TRUE), -- Kepatuhan perawat melakukan pelaporan menggunakan format SBAR dan read back
('R01', 36, TRUE), -- Kepatuhan dokter dalam pemberian site marking
('R01', 37, TRUE), -- Kepatuhan cuci tangan
('R01', 38, TRUE), -- Kepatuhan upaya pencegahan resiko jatuh
('R01', 39, TRUE), -- Waktu respon penanganan gangguan sistem sysmed RS
('R01', 40, TRUE); -- Ketepatan serah terima pasien post op dengan perawat recovery room

-- pertanyaan
INSERT INTO pertanyaan (pertanyaan) VALUES
('Bagaimana pemahaman Saudara tentang kemudahan prosedur pelayanan di Rumah Sakit Daerah Kalisat?'),
('Bagaimana pemahaman Saudara tentang kejelasan prosedur pelayanan di Rumah Sakit Daerah Kalisat?'),
('Bagaimana pendapat Saudara tentang kecepatan dan ketepatan pelayanan di Rumah Sakit Daerah Kalisat?'),
('Bagaimana pendapat Saudara tentang kesopanan dan keramahan petugas dalam memberikan pelayanan?'),
('Bagaimana pendapat Saudara tentang kewajaran biaya untuk mendapatkan pelayanan?'),
('Bagaimana pendapat Saudara tentang kenyamanan dan kebersihan di lingkungan Rumah Sakit Daerah Kalisat?'),
('Bagaimana pendapat Saudara tentang keamanan pelayanan di ruangan ini'),
('Apakah pertimbangan Anda memilih dirawat di RumahSakit Daerah Kalisat?'),
('Menurut pendapat Anda, hal-hal apa yang seharusnya menjadi perhatian rumah sakit dan sedapat mungkin dikembangkan?'),
('Apakah yang anda inginkan untuk peningkatan kualitas pelayanan di rumah sakit ini?'),
('Apakah petugas kesehatan menjelaskan tentang penggunaan gelang identifikasi'),
('Apakah petugas kesehatan memperkenalkan diri saat mengunjungi pasien'),
('Apakah petugas menjelaskan tentang tindakan yang akan dilakukan terhadap pasien'),
('Apakah petugas kesehatan memberikan penjelasan tentang cara cuci tangan menggunakan 6 langkah'),
('Apakah petugas kesehatan menjelaskan tentang penanganan resiko jatuh'),
('Silahkan berikan kritik dan saran ');

-- user

INSERT INTO `user` (id_user, username, password, id_ruangan, nama_ruangan) VALUES
('U00', 'superadmin', 'superadmin123', 'SP00', 'Super Admin'),
('U01', 'ruang_nifas', 'nifas123', 'R01', 'Nifas'),
('U02', 'ruang_perinatologi', 'perinatologi123', 'R02', 'Perinatologi'),
('U03', 'ruang_vk', 'vk123', 'R03', 'VK'),
('U04', 'ruang_farmasi', 'farmasi123', 'R04', 'Farmasi'),
('U05', 'ruang_laboratorium', 'laboratorium123', 'R05', 'Laboratorium'),
('U06', 'ruang_anak', 'anak123', 'R06', 'Anak'),
('U07', 'ruang_cssd', 'cssd123', 'R07', 'CSSD'),
('U08', 'ruang_interna', 'interna123', 'R08', 'Interna'),
('U09', 'ruang_ibs', 'ibs123', 'R09', 'IBS'),
('U10', 'ruang_icu', 'icu123', 'R10', 'ICU'),
('U11', 'ruang_igd', 'igd123', 'R11', 'IGD'),
('U12', 'ruang_ipsrs', 'ipsrs123', 'R12', 'IPSRS'),
('U13', 'ruang_laundry', 'laundry123', 'R13', 'Laundry'),
('U14', 'ruang_bedah', 'bedah123', 'R14', 'Bedah'),
('U15', 'ruang_gizi', 'gizi123', 'R15', 'Gizi'),
('U16', 'ruang_rajal', 'rajal123', 'R16', 'Rajal'),
('U17', 'ruang_vip', 'vip123', 'R17', 'VIP'),
('U18', 'ruang_rm', 'rm123', 'R18', 'RM'),
('U19', 'ruang_radiologi', 'radiologi123', 'R19', 'Radiologi'),
('U20', 'ruang_keuangan', 'keuangan123', 'R20', 'Keuangan'),
('U21', 'ruang_kantor', 'kantor123', 'R21', 'Kantor');

-- biodata pasien

INSERT INTO bio_pasien (id_ruangan, no_rm, umur, jenis_kelamin, pendidikan, pekerjaan) VALUES
('R01', 'RM001', 30, 'Laki-laki', 'S1', 'Karyawan'),
('R02', 'RM002', 45, 'Perempuan', 'SMA', 'Ibu Rumah Tangga'),
('R03', 'RM003', 60, 'Laki-laki', 'SMP', 'Pensiunan');

-- pilihan jawaban

INSERT INTO pilihan_jawaban (id_pertanyaan, pilihan, nilai) VALUES
(1, 'Tidak Mudah', 2),
(1, 'Kurang Mudah', 3),
(1, 'Mudah', 4),
(1, 'Sangat Mudah', 5),
(2, 'Tidak Jelas', 2),
(2, 'Kurang Jelas', 3),
(2, 'Jelas', 4),
(2, 'Sangat Jelas', 5),
(3, 'Tidak Cepat', 2),
(3, 'Kurang Cepat', 3),
(3, 'Cepat', 4),
(3, 'Sangat Cepat', 5),
(4, 'Tidak Sopan dan Tidak Ramah', 2),
(4, 'Kurang SOpan dan Kurang Ramah', 3),
(4, 'Sopan dan Ramah', 4),
(4, 'Sangat Sopan dan Ramah', 5),
(5, 'Tidak Wajar', 2),
(5, 'Kurang Wajar', 3),
(5, 'Wajar', 4),
(5, 'Sangat Wajar', 5),
(6, 'Tidak Nyaman', 2),
(6, 'Kurang Nyaman', 3),
(6, 'Nyaman', 4),
(6, 'Sangat Nyaman', 5),
(7, 'Tidak Aman', 2),
(7, 'Kurang Aman', 3),
(7, 'Aman', 4),
(7, 'Sangat Aman', 5),
(8, 'Pelayanan yang baik', 2),
(8, 'Bangunan rumah sakit, peralatan yang lengkap dan canggih', 3),
(8, 'Harga pelayanan yang terjangkau', 4),
(8, 'Dekat dengan lokasi tempat tinggal', 5),
(9, 'Penataan kamar', 1),
(9, 'Penataan parkir', 2),
(9, 'Penambahan kamar', 3),
(9, 'Kebersihan bangunan', 4),
(9, 'Pengadaan pelayanan umum seperti wartel, mini market, dll', 5),
(10, 'Pelayanan yang cepat dengan harga terjangkau', 1),
(10, 'Kebersihan bangunan', 2),
(10, 'Keramahan, ketrampilan dan kemampuan petugas', 3),
(10, 'Adanya sarana pelayanan umum', 4),
(10, 'Peralatan kedokteran yang canggih', 5),
(11, 'Ya', 10),
(11, 'Tidak', 0),
(12, 'Ya', 10),
(12, 'Tidak', 0),
(13, 'Ya', 10),
(13, 'Tidak', 0),
(14, 'Ya', 10),
(14, 'Tidak', 0),
(15, 'Ya', 10),
(15, 'Tidak', 0);

-- jawaban

INSERT INTO jawaban (tanggal, id_pasien, id_pertanyaan, id_pilihan, hasil_nilai) VALUES
(CURRENT_DATE, 1, 1, 4, '5'),
(CURRENT_DATE, 1, 2, 8, '5'),
(CURRENT_DATE, 1, 3, 12, '5'),
(CURRENT_DATE, 1, 4, 16, '5'),
(CURRENT_DATE, 1, 5, 20, '5'),
(CURRENT_DATE, 1, 6, 24, '5'),
(CURRENT_DATE, 1, 7, 28, '5'),
(CURRENT_DATE, 1, 8, 32, '5'),
(CURRENT_DATE, 1, 9, 37, '5'),
(CURRENT_DATE, 1, 10, 42, '5'),
(CURRENT_DATE, 1, 11, 43, '10'),
(CURRENT_DATE, 1, 12, 45, '10'),
(CURRENT_DATE, 1, 13, 47, '10'),
(CURRENT_DATE, 1, 14, 49, '10'),
(CURRENT_DATE, 1, 15, 51, '10'),
(CURRENT_DATE, 1, 16, NULL, 'Tidak ada sudah bagus');

-- mutu ruangan

INSERT INTO mutu_ruangan (tanggal, id_indikator_ruangan, total_pasien, pasien_sesuai) VALUES
-- Nifas Day 1
(CURRENT_DATE, 1, 100, 88),  -- Kejadian retensi urin pada pasien post partum
(CURRENT_DATE, 2, 100, 92), -- Kepatuhan Cuci tangan
(CURRENT_DATE, 3, 100, 85), -- Kepatuhan Penggunaan APD
(CURRENT_DATE, 4, 100, 95), -- Identifikasi Pasien
(CURRENT_DATE, 5, 100, 90), -- Ketepatan Waktu Visite Dokter
(CURRENT_DATE, 6, 100, 83), -- Kepatuhan Terhadap Clinical Pathway
(CURRENT_DATE, 7, 100, 97), -- Kepatuhan Upaya Pencegahan Resiko Jatuh
(CURRENT_DATE, 8, 100, 80), -- Tanggap Komplain
(CURRENT_DATE, 9, 100, 94), -- Kepuasan Masyarakat
(CURRENT_DATE, 10, 100, 87), -- Ketepatan identifikasi pasien
(CURRENT_DATE, 11, 100, 91), -- Kepatuhan perawat melakukan pelaporan menggunakan format SBAR dan read back
(CURRENT_DATE, 12, 100, 84), -- Kepatuhan dokter dalam pemberian site marking
(CURRENT_DATE, 13, 100, 96), -- Kepatuhan cuci tangan
(CURRENT_DATE, 14, 100, 89), -- Kepatuhan upaya pencegahan resiko jatuh
(CURRENT_DATE, 15, 100, 82), -- Waktu respon penanganan gangguan sistem sysmed RS
(CURRENT_DATE, 16, 100, 93); -- Ketepatan serah terima pasien post op dengan perawat recovery room

SET @r=0;
UPDATE pertanyaan SET urutan = (@r:=@r+1) ORDER BY id_pertanyaan ASC;