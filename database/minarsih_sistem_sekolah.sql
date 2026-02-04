/*
 Navicat Premium Data Transfer

 Source Server         : MySQL_Adham
 Source Server Type    : MySQL
 Source Server Version : 80030 (8.0.30)
 Source Host           : localhost:3306
 Source Schema         : minarsih_sistem_sekolah

 Target Server Type    : MySQL
 Target Server Version : 80030 (8.0.30)
 File Encoding         : 65001

 Date: 22/01/2025 06:23:06
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for berita
-- ----------------------------
DROP TABLE IF EXISTS `berita`;
CREATE TABLE `berita`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `foto` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `judul` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `isi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of berita
-- ----------------------------

-- ----------------------------
-- Table structure for buku
-- ----------------------------
DROP TABLE IF EXISTS `buku`;
CREATE TABLE `buku`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `judul` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `pengarang` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `penerbit` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tahun_terbit` year NOT NULL,
  `stok` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of buku
-- ----------------------------
INSERT INTO `buku` VALUES (1, 'Si kancil', 'Pak jiwa', 'pak 321', 2000, 21, '2025-01-20 22:40:49', '2025-01-21 01:39:26');
INSERT INTO `buku` VALUES (2, 'Fana', 'M Hady', 'Pak Ruly', 2014, 4, '2025-01-20 22:41:38', '2025-01-20 22:41:38');

-- ----------------------------
-- Table structure for failed_jobs
-- ----------------------------
DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE `failed_jobs`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `failed_jobs_uuid_unique`(`uuid` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of failed_jobs
-- ----------------------------

-- ----------------------------
-- Table structure for guru
-- ----------------------------
DROP TABLE IF EXISTS `guru`;
CREATE TABLE `guru`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `nip` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `kelas` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `jurusan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_lahir` date NOT NULL,
  `alamat` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `no_hp` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `guru_nip_unique`(`nip` ASC) USING BTREE,
  INDEX `guru_user_id_foreign`(`user_id` ASC) USING BTREE,
  CONSTRAINT `guru_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of guru
-- ----------------------------
INSERT INTO `guru` VALUES (1, 6, '19880515 201501 2 001', 'X', 'Rakayasa Perangkat Lunak', '2025-01-31', '----', '0819281928', '2025-01-20 18:34:04', '2025-01-20 18:34:04');

-- ----------------------------
-- Table structure for inventaris
-- ----------------------------
DROP TABLE IF EXISTS `inventaris`;
CREATE TABLE `inventaris`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama_inventaris` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `kategori` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `jumlah` int NOT NULL,
  `deskripsi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `status` enum('Tersedia','Tidak Tersedia') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `gambar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of inventaris
-- ----------------------------
INSERT INTO `inventaris` VALUES (1, 'Microscope', 'Alat Laboratorium', 5, 'Digunakan untuk melihat objek kecil.', 'Tersedia', 'microscope.jpg', '2025-01-20 15:12:48', '2025-01-20 15:12:48');
INSERT INTO `inventaris` VALUES (2, 'Projector', 'Elektronik', 2, 'Digunakan untuk presentasi.', 'Tersedia', 'proyektor.jpg', '2025-01-20 15:12:48', '2025-01-20 15:12:48');
INSERT INTO `inventaris` VALUES (3, 'Whiteboard', 'Peralatan', 10, 'Digunakan untuk menulis di ruang kelas.', 'Tidak Tersedia', 'whiteboard.jpg', '2025-01-20 15:12:48', '2025-01-20 15:12:48');
INSERT INTO `inventaris` VALUES (4, 'VR', 'Inventaris Lab Komputer', 2000, 'INI BARANG MAHAL', 'Tersedia', '1737423886_TC MACAROON.png', '2025-01-21 01:44:47', '2025-01-21 01:44:47');

-- ----------------------------
-- Table structure for kelas
-- ----------------------------
DROP TABLE IF EXISTS `kelas`;
CREATE TABLE `kelas`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint NOT NULL,
  `nama_kelas` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `jurusan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `wali_kelas` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `kapasitas_kelas` double NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of kelas
-- ----------------------------
INSERT INTO `kelas` VALUES (1, 6, 'X', 'Rekayasa Perangkat Lunak', 'Guru', 20, '2025-01-22 03:22:50', NULL);

-- ----------------------------
-- Table structure for laboratorium
-- ----------------------------
DROP TABLE IF EXISTS `laboratorium`;
CREATE TABLE `laboratorium`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `labor` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('kosong','terpakai') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'kosong',
  `start` datetime NULL DEFAULT NULL,
  `end` datetime NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 7 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of laboratorium
-- ----------------------------
INSERT INTO `laboratorium` VALUES (1, 'TKJ', 'terpakai', '2024-11-15 09:00:00', '2024-11-15 12:00:00', '2025-01-20 15:12:48', '2025-01-20 15:12:48');
INSERT INTO `laboratorium` VALUES (2, 'TKJ', 'kosong', '2024-11-15 13:00:00', '2024-11-15 16:00:00', '2025-01-20 15:12:48', '2025-01-20 15:12:48');
INSERT INTO `laboratorium` VALUES (3, 'MM', 'kosong', '2024-11-15 09:00:00', '2024-11-15 12:00:00', '2025-01-20 15:12:48', '2025-01-20 15:12:48');
INSERT INTO `laboratorium` VALUES (4, 'MM', 'terpakai', '2024-11-15 13:00:00', '2024-11-15 16:00:00', '2025-01-20 15:12:48', '2025-01-20 15:12:48');
INSERT INTO `laboratorium` VALUES (5, 'RPL', 'kosong', '2024-11-16 09:00:00', '2024-11-16 12:00:00', '2025-01-20 15:12:48', '2025-01-20 15:12:48');
INSERT INTO `laboratorium` VALUES (6, 'RPL', 'terpakai', '2024-11-16 13:00:00', '2024-11-16 16:00:00', '2025-01-20 15:12:48', '2025-01-20 15:12:48');

-- ----------------------------
-- Table structure for laporan_kerusakan
-- ----------------------------
DROP TABLE IF EXISTS `laporan_kerusakan`;
CREATE TABLE `laporan_kerusakan`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama_pelapor` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_alat` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `deskripsi_kerusakan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_laporan` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of laporan_kerusakan
-- ----------------------------
INSERT INTO `laporan_kerusakan` VALUES (1, 'John Doe', 'Microscope', 'Lensa pecah dan sulit digunakan.', '2025-01-15', '2025-01-20 15:12:48', '2025-01-20 15:12:48');
INSERT INTO `laporan_kerusakan` VALUES (2, 'Jane Smith', 'Projector', 'Tidak bisa menyala meskipun sudah dihubungkan ke listrik.', '2025-01-16', '2025-01-20 15:12:48', '2025-01-20 15:12:48');
INSERT INTO `laporan_kerusakan` VALUES (3, 'Alice Johnson', 'Laptop', 'Keyboard beberapa tombol tidak berfungsi.', '2025-01-17', '2025-01-20 15:12:48', '2025-01-20 15:12:48');
INSERT INTO `laporan_kerusakan` VALUES (4, 'suryadi', 'Microscope', 'tastastats', '2025-01-31', '2025-01-21 01:42:04', '2025-01-21 01:42:04');

-- ----------------------------
-- Table structure for magang_siswa
-- ----------------------------
DROP TABLE IF EXISTS `magang_siswa`;
CREATE TABLE `magang_siswa`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `perusahaan_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `status` enum('Menunggu','Disetujui','Ditolak') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Menunggu',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of magang_siswa
-- ----------------------------
INSERT INTO `magang_siswa` VALUES (1, 'Mahendra Wahyu Avillanda', '2', '2025-01-01', '2025-03-31', 'Disetujui', '2025-01-20 22:10:02', '2025-01-20 22:29:12');
INSERT INTO `magang_siswa` VALUES (2, 'Mahendra Wahyu Avillanda', '3', '2025-01-01', '2025-06-30', 'Ditolak', '2025-01-20 22:16:43', '2025-01-20 22:33:47');

-- ----------------------------
-- Table structure for mata_pelajaran
-- ----------------------------
DROP TABLE IF EXISTS `mata_pelajaran`;
CREATE TABLE `mata_pelajaran`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint NOT NULL COMMENT 'Id User Guru',
  `kelas_id` bigint NOT NULL COMMENT 'Id User Guru',
  `nama_kelas` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_guru` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `nama_mata_pelajaran` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `jadwal` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of mata_pelajaran
-- ----------------------------
INSERT INTO `mata_pelajaran` VALUES (1, 6, 1, 'X', 'Guru', 'Basis Data', 'Senin, Rabu, Jum\'at', '2025-01-22 01:10:54', '2025-01-22 01:10:54');
INSERT INTO `mata_pelajaran` VALUES (4, 6, 1, 'X', 'Guru', 'Web Dinamis', 'Senin, Rabu, Jum\'at', '2025-01-22 01:23:07', '2025-01-22 01:23:07');

-- ----------------------------
-- Table structure for migrations
-- ----------------------------
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 22 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of migrations
-- ----------------------------
INSERT INTO `migrations` VALUES (1, '2014_10_12_000000_create_users_table', 1);
INSERT INTO `migrations` VALUES (2, '2014_10_12_100000_create_password_resets_table', 1);
INSERT INTO `migrations` VALUES (3, '2019_08_19_000000_create_failed_jobs_table', 1);
INSERT INTO `migrations` VALUES (4, '2019_12_14_000001_create_personal_access_tokens_table', 1);
INSERT INTO `migrations` VALUES (5, '2025_01_17_134136_create_laboratorium_table', 1);
INSERT INTO `migrations` VALUES (6, '2025_01_17_134216_create_pinjam_labor_table', 1);
INSERT INTO `migrations` VALUES (7, '2025_01_17_141742_create_laporan_kerusakan_table', 1);
INSERT INTO `migrations` VALUES (8, '2025_01_17_142456_create_inventaris_table', 1);
INSERT INTO `migrations` VALUES (9, '2025_01_18_045831_create_pinjam_inventaris_table', 1);
INSERT INTO `migrations` VALUES (10, '2025_01_19_002917_create_ppdb_calon_siswa_table', 1);
INSERT INTO `migrations` VALUES (11, '2025_01_19_002933_create_perusahaan_table', 1);
INSERT INTO `migrations` VALUES (12, '2025_01_19_002935_create_siswa_table', 1);
INSERT INTO `migrations` VALUES (13, '2025_01_19_002949_create_buku_table', 1);
INSERT INTO `migrations` VALUES (14, '2025_01_19_003002_create_peminjaman_table', 1);
INSERT INTO `migrations` VALUES (15, '2025_01_19_003016_create_magang_siswa_table', 1);
INSERT INTO `migrations` VALUES (16, '2025_01_20_010009_create_berita_table', 1);
INSERT INTO `migrations` VALUES (18, '2025_01_21_175759_create_spps_table', 2);
INSERT INTO `migrations` VALUES (19, '2025_01_21_195513_create_gurus_table', 3);
INSERT INTO `migrations` VALUES (20, '2025_01_21_201445_create_kelas_table', 4);
INSERT INTO `migrations` VALUES (21, '2025_01_21_220558_create_mata_pelajarans_table', 5);

-- ----------------------------
-- Table structure for password_resets
-- ----------------------------
DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE `password_resets`  (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  INDEX `password_resets_email_index`(`email` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of password_resets
-- ----------------------------

-- ----------------------------
-- Table structure for peminjaman
-- ----------------------------
DROP TABLE IF EXISTS `peminjaman`;
CREATE TABLE `peminjaman`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `buku_id` bigint UNSIGNED NULL DEFAULT NULL,
  `tanggal_pinjam` date NOT NULL,
  `tanggal_kembali` date NULL DEFAULT NULL,
  `status` enum('Menunggu','Disetujui','Ditolak','Dikembalikan') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Menunggu',
  `tujuan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `peminjaman_buku_id_foreign`(`buku_id` ASC) USING BTREE,
  CONSTRAINT `peminjaman_buku_id_foreign` FOREIGN KEY (`buku_id`) REFERENCES `buku` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of peminjaman
-- ----------------------------

-- ----------------------------
-- Table structure for personal_access_tokens
-- ----------------------------
DROP TABLE IF EXISTS `personal_access_tokens`;
CREATE TABLE `personal_access_tokens`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `personal_access_tokens_token_unique`(`token` ASC) USING BTREE,
  INDEX `personal_access_tokens_tokenable_type_tokenable_id_index`(`tokenable_type` ASC, `tokenable_id` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of personal_access_tokens
-- ----------------------------

-- ----------------------------
-- Table structure for perusahaan
-- ----------------------------
DROP TABLE IF EXISTS `perusahaan`;
CREATE TABLE `perusahaan`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama_perusahaan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `alamat` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_pembimbing` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `no_perusahaan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of perusahaan
-- ----------------------------
INSERT INTO `perusahaan` VALUES (2, 'PT. Jaya Sejati', 'indonesia', 'Pak Sutarjo', '08937162371283', '2025-01-20 22:09:29', '2025-01-20 22:09:29');
INSERT INTO `perusahaan` VALUES (3, 'PT. Laskar Pelangi', 'Jl. Jawa', 'Pak Riki', '087641352637', '2025-01-20 22:16:19', '2025-01-20 22:16:19');

-- ----------------------------
-- Table structure for pinjam_inventaris
-- ----------------------------
DROP TABLE IF EXISTS `pinjam_inventaris`;
CREATE TABLE `pinjam_inventaris`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `kelas` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `inventaris` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_peminjaman` date NOT NULL,
  `tujuan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('Menunggu','Disetujui','Ditolak') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Menunggu',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of pinjam_inventaris
-- ----------------------------
INSERT INTO `pinjam_inventaris` VALUES (1, 'Suryadi', 'XII RPL 1', 'Microscope', '2025-01-31', '----', 'Ditolak', '2025-01-21 01:41:45', '2025-01-21 01:44:13');

-- ----------------------------
-- Table structure for pinjam_labor
-- ----------------------------
DROP TABLE IF EXISTS `pinjam_labor`;
CREATE TABLE `pinjam_labor`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `laboratorium_id` bigint UNSIGNED NOT NULL,
  `tanggal` date NOT NULL,
  `waktu` time NOT NULL,
  `keperluan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `pinjam_labor_laboratorium_id_foreign`(`laboratorium_id` ASC) USING BTREE,
  CONSTRAINT `pinjam_labor_laboratorium_id_foreign` FOREIGN KEY (`laboratorium_id`) REFERENCES `laboratorium` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of pinjam_labor
-- ----------------------------
INSERT INTO `pinjam_labor` VALUES (1, 'John', 1, '2025-01-20', '15:12:48', 'Praktikum', '2025-01-20 15:12:48', '2025-01-20 15:12:48');
INSERT INTO `pinjam_labor` VALUES (2, 'Doe', 2, '2025-01-20', '15:12:48', 'Praktikum', '2025-01-20 15:12:48', '2025-01-20 15:12:48');

-- ----------------------------
-- Table structure for ppdb_calon_siswa
-- ----------------------------
DROP TABLE IF EXISTS `ppdb_calon_siswa`;
CREATE TABLE `ppdb_calon_siswa`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_lahir` date NOT NULL,
  `alamat` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `sekolah_asal` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `no_hp` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status_pendaftaran` enum('Menunggu','Diterima','Ditolak') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Menunggu',
  `tanggal_pendaftaran` date NOT NULL,
  `nilai_rapor` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 17 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of ppdb_calon_siswa
-- ----------------------------
INSERT INTO `ppdb_calon_siswa` VALUES (16, 'Andrian', '2025-01-31', '---', 'SMP Padang', '0823872837', 'adhamnugroho5@gmail.com', 'Diterima', '2025-01-21', 'NILAI-RAPOR-1737447427-005d0.pdf', '2025-01-21 08:07:54', '2025-01-21 09:43:26');

-- ----------------------------
-- Table structure for siswa
-- ----------------------------
DROP TABLE IF EXISTS `siswa`;
CREATE TABLE `siswa`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `kelas_id` bigint NULL DEFAULT NULL,
  `nisn` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `kelas` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `jurusan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_lahir` date NOT NULL,
  `alamat` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `no_hp` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `siswa_nisn_unique`(`nisn` ASC) USING BTREE,
  INDEX `siswa_user_id_foreign`(`user_id` ASC) USING BTREE,
  CONSTRAINT `siswa_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of siswa
-- ----------------------------
INSERT INTO `siswa` VALUES (1, 7, 1, '9876543210', 'X', 'Rekayasa Perangkat Lunak', '2025-01-31', '----', '0819281928', '2025-01-21 01:34:04', '2025-01-21 21:02:21');
INSERT INTO `siswa` VALUES (2, 8, 1, '829382938923', 'X', 'Rekayasa Perangkat Lunak', '2025-01-31', '------', '0891281928912', '2025-01-21 21:14:39', '2025-01-21 21:18:37');

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('admin','guru','siswa') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `menu` enum('ppdb','sistem_akademik','perpus','labor','magang') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `users_email_unique`(`email` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 9 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of users
-- ----------------------------
INSERT INTO `users` VALUES (1, 'AdminPPDB', 'adminPpdb@gmail.com', '$2y$10$xI5FTpANfp2r6tCyry8ZhOyddV3670qygsxnG2OMieLASfow2yNna', 'admin', 'ppdb', '2025-01-20 15:12:47', 'eQWZjCMfSx3YqW3LB2vApvbbjWY4JOUS783WKNtPjaH1PAjNF70uqDHNhgjD', '2025-01-20 15:12:47', '2025-01-20 15:12:47');
INSERT INTO `users` VALUES (2, 'AdminSistemAkademik', 'adminSa@gmail.com', '$2y$10$GgRK3pRZ9TmT5CNb8wVxAuq4oCjax.8./I74jRfdjavJpr8EmcMQy', 'admin', 'sistem_akademik', '2025-01-20 15:12:47', 'gx4hKxOZ96ZzhBtCCwNORtZEbWQPnmaw9FlNVSKsW8IPH4MSw8L1K9HT78Aw', '2025-01-20 15:12:47', '2025-01-20 15:12:47');
INSERT INTO `users` VALUES (3, 'AdminPerpus', 'adminPerpus@gmail.com', '$2y$10$2VCuB25NevaixiQhU5PAvemkMmtHVVRzIQ1KtV.T16DbzOfTtO6G.', 'admin', 'perpus', '2025-01-20 15:12:47', 'ke1LDlbrLLA2I81uhTH0Zrs4Cbio2pUhzDH4IsFwSA9GyzxhZpC1t3S9Yqna', '2025-01-20 15:12:47', '2025-01-20 15:12:47');
INSERT INTO `users` VALUES (4, 'AdminLabor', 'adminLab@gmail.com', '$2y$10$XoJMxU4G0Zze/2b/kSvrd.Qp9yV6cfUKmFmgYljY08iVQpIDxrXWi', 'admin', 'labor', '2025-01-20 15:12:47', 'R5gMEt9g9f5AadPmiUD8NFRIpTSxHQcvNx07yLXpRRmcn7aIshvlRvVQiJp2', '2025-01-20 15:12:47', '2025-01-20 15:12:47');
INSERT INTO `users` VALUES (5, 'AdminMagang', 'adminMagang@gmail.com', '$2y$10$W.jjmTEfz7YmKObuyK1Sd.i3HbIe5A0p6vNu8nXSV/Gtrp50J4/jq', 'admin', 'magang', '2025-01-20 15:12:48', 'TmqOqbzZA63Mp0hSVmo2sZzbBBxszzGUQT9Ko1xpohFE7bPOHFNBDZrze7VZ', '2025-01-20 15:12:48', '2025-01-20 15:12:48');
INSERT INTO `users` VALUES (6, 'Guru', 'guru@gmail.com', '$2y$10$.ERmDgC4m7Ec9gDBPEVlMOj1X4KXzbAsTvOg5rPFGCumj9uJXa5ui', 'guru', NULL, '2025-01-20 15:12:48', 'nmmS2Cp2dPMqGg7siMMVXG0UngEJhEa90wK4QO0346KgtGWJKVxId34UlrGr', '2025-01-20 15:12:48', '2025-01-20 15:12:48');
INSERT INTO `users` VALUES (7, 'Siswa', 'siswa@gmail.com', '$2y$10$28aSs9YVKMzoGcvizNC7BemhwCYP4tYCyVEkIym5.lYWpsL46jbPm', 'siswa', NULL, NULL, NULL, '2025-01-21 01:34:04', '2025-01-21 18:17:12');
INSERT INTO `users` VALUES (8, 'Andrian', 'andrian@gmail.com', '$2y$10$jZIgdt9SrJHR.b7c6ZrRGeoDN5/b6NiiqYpr03Y/Vp1gnmcIbYSfG', 'siswa', NULL, NULL, NULL, '2025-01-21 21:14:39', '2025-01-21 21:14:39');

SET FOREIGN_KEY_CHECKS = 1;
