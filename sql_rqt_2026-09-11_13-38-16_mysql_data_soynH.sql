/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19  Distrib 10.11.10-MariaDB, for Linux (x86_64)
--
-- Host: localhost    Database: sql_rqt
-- ------------------------------------------------------
-- Server version	10.11.10-MariaDB-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `absensi`
--

DROP TABLE IF EXISTS `absensi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `absensi` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nis` varchar(20) DEFAULT NULL,
  `waktu_masuk` datetime DEFAULT NULL,
  `keterangan` varchar(20) DEFAULT 'Hadir',
  `input_by` varchar(50) DEFAULT NULL,
  `waktu_pulang` datetime DEFAULT NULL,
  `status_kehadiran` enum('Tepat Waktu','Terlambat') DEFAULT 'Tepat Waktu',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `absensi`
--

LOCK TABLES `absensi` WRITE;
/*!40000 ALTER TABLE `absensi` DISABLE KEYS */;
INSERT INTO `absensi` VALUES
(1,'12526.1.005','2026-08-03 21:52:28','Bolos',NULL,NULL,'Terlambat'),
(2,'12627.1.019','2026-08-03 21:52:31','Bolos',NULL,NULL,'Terlambat'),
(3,'12627.1.018','2026-08-03 21:52:35','Bolos',NULL,NULL,'Terlambat'),
(4,'12627.1.016','2026-08-03 21:52:46','Bolos',NULL,NULL,'Terlambat'),
(5,'12627.1.015','2026-08-03 21:52:58','Bolos',NULL,NULL,'Terlambat'),
(6,'12627.1.014','2026-08-03 21:53:03','Bolos',NULL,NULL,'Terlambat'),
(7,'12526.1.006','2026-08-03 21:53:07','Bolos',NULL,NULL,'Terlambat'),
(8,'12627.1.013','2026-08-03 21:53:11','Bolos',NULL,NULL,'Terlambat'),
(9,'12526.1.007','2026-08-03 21:53:15','Hadir',NULL,'2026-08-03 21:53:29','Terlambat'),
(10,'12526.1.001','2026-08-03 21:53:25','Hadir',NULL,'2026-08-03 21:53:37','Terlambat'),
(11,'12526.1.002','2026-08-03 21:53:51','Bolos',NULL,NULL,'Terlambat'),
(12,'12627.1.012','2026-08-03 21:53:55','Bolos',NULL,NULL,'Terlambat'),
(13,'12526.1.004','2026-08-03 21:53:59','Bolos',NULL,NULL,'Terlambat'),
(14,'12526.1.003','2026-08-03 21:54:06','Bolos',NULL,NULL,'Terlambat'),
(15,'12627.1.011','2026-08-03 21:54:13','Bolos',NULL,NULL,'Terlambat'),
(16,'12627.1.010','2026-08-03 21:54:22','Bolos',NULL,NULL,'Terlambat'),
(17,'12627.1.009','2026-08-03 21:54:28','Bolos',NULL,NULL,'Terlambat'),
(18,'12627.1.008','2026-08-03 21:54:34','Bolos',NULL,NULL,'Terlambat'),
(19,'12627.1.007','2026-08-03 21:54:43','Bolos',NULL,NULL,'Terlambat'),
(20,'12627.1.006','2026-08-03 21:54:47','Bolos',NULL,NULL,'Terlambat'),
(21,'12627.1.005','2026-08-03 21:54:51','Bolos',NULL,NULL,'Terlambat'),
(22,'12627.1.004','2026-08-03 21:54:55','Bolos',NULL,NULL,'Terlambat'),
(23,'12627.1.003','2026-08-03 21:55:00','Bolos',NULL,NULL,'Terlambat'),
(24,'12627.1.002','2026-08-03 21:55:06','Bolos',NULL,NULL,'Terlambat'),
(25,'12627.1.001','2026-08-03 21:55:11','Bolos',NULL,NULL,'Terlambat'),
(26,'12526.1.008','2026-08-03 21:55:15','Hadir',NULL,NULL,'Terlambat'),
(27,'42627.1.002','2026-08-05 11:38:50','Hadir',NULL,NULL,'Terlambat'),
(28,'42526.1.001','2026-08-05 11:39:06','Hadir',NULL,NULL,'Terlambat'),
(29,'42425.01.00','2026-08-05 11:39:12','Hadir',NULL,NULL,'Terlambat'),
(30,'42526.1.006','2026-08-05 11:39:15','Hadir',NULL,NULL,'Terlambat'),
(31,'12526.1.008','2026-08-13 12:03:21','Hadir',NULL,NULL,'Terlambat'),
(32,'12526.1.005','2026-09-04 14:33:56','Hadir',NULL,'2026-09-04 14:33:59','Terlambat'),
(33,'32526.1.001','2026-09-04 14:35:13','Hadir',NULL,'2026-09-04 14:35:16','Terlambat'),
(34,'42425.01.00','2026-09-10 11:22:37','Hadir',NULL,NULL,'Terlambat');
/*!40000 ALTER TABLE `absensi` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `absensi_guru`
--

DROP TABLE IF EXISTS `absensi_guru`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `absensi_guru` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nip` varchar(20) NOT NULL,
  `waktu_masuk` datetime DEFAULT NULL,
  `waktu_pulang` datetime DEFAULT NULL,
  `keterangan` varchar(20) DEFAULT 'Hadir',
  `status_kehadiran` enum('Tepat Waktu','Terlambat') DEFAULT 'Tepat Waktu',
  `input_by` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `absensi_guru`
--

LOCK TABLES `absensi_guru` WRITE;
/*!40000 ALTER TABLE `absensi_guru` DISABLE KEYS */;
INSERT INTO `absensi_guru` VALUES
(1,'2601004','2026-08-04 13:03:29',NULL,'Hadir','Terlambat',NULL),
(2,'2306001','2026-08-04 13:03:43',NULL,'Hadir','Terlambat',NULL),
(3,'2501005','2026-08-05 11:38:57',NULL,'Hadir','Terlambat',NULL),
(4,'2506002','2026-09-04 14:33:34','2026-09-04 14:33:42','Hadir','Terlambat',NULL),
(5,'2601003','2026-09-04 14:34:15','2026-09-04 14:39:22','Hadir','Terlambat',NULL),
(6,'1901001','2026-09-04 14:34:21','2026-09-04 14:39:29','Hadir','Terlambat',NULL),
(7,'2503001','2026-09-04 14:36:48',NULL,'Hadir','Terlambat',NULL),
(8,'2306001','2026-09-04 14:37:12',NULL,'Hadir','Terlambat',NULL),
(9,'2602001','2026-09-04 14:37:55','2026-09-04 14:38:05','Hadir','Terlambat',NULL),
(10,'2402002','2026-09-04 14:38:01','2026-09-04 14:38:09','Hadir','Terlambat',NULL),
(11,'2502003','2026-09-04 14:38:14',NULL,'Hadir','Terlambat',NULL),
(12,'2602004','2026-09-04 14:38:21','2026-09-04 14:38:28','Hadir','Terlambat',NULL),
(13,'2602005','2026-09-04 14:38:32','2026-09-04 14:38:37','Hadir','Terlambat',NULL),
(14,'2602006','2026-09-04 14:38:42','2026-09-04 14:38:45','Hadir','Terlambat',NULL),
(15,'2603004','2026-09-04 14:39:12',NULL,'Hadir','Terlambat',NULL),
(16,'2604004','2026-09-04 14:39:17',NULL,'Hadir','Terlambat',NULL),
(17,'2601004','2026-09-04 14:39:39','2026-09-04 14:39:44','Hadir','Terlambat',NULL),
(18,'2306001','2026-09-05 08:32:33',NULL,'Hadir','Terlambat',NULL),
(19,'2602004','2026-09-05 08:32:40',NULL,'Hadir','Terlambat',NULL),
(20,'2602001','2026-09-05 08:32:45',NULL,'Hadir','Terlambat',NULL),
(21,'2502003','2026-09-05 08:32:55',NULL,'Hadir','Terlambat',NULL),
(22,'2602005','2026-09-05 08:33:06',NULL,'Hadir','Terlambat',NULL),
(23,'2402002','2026-09-05 08:33:22',NULL,'Hadir','Terlambat',NULL),
(24,'2604004','2026-09-05 08:33:52',NULL,'Hadir','Terlambat',NULL),
(25,'2506002','2026-09-05 08:34:08',NULL,'Hadir','Terlambat',NULL),
(26,'2601004','2026-09-05 08:34:19',NULL,'Hadir','Terlambat',NULL),
(27,'2601003','2026-09-05 08:34:31',NULL,'Hadir','Terlambat',NULL),
(28,'1901001','2026-09-05 08:34:40',NULL,'Hadir','Terlambat',NULL),
(29,'2503001','2026-09-05 08:35:06',NULL,'Hadir','Terlambat',NULL),
(30,'2602001','2026-09-07 08:04:01',NULL,'Hadir','Terlambat',NULL),
(31,'2602005','2026-09-07 08:04:07',NULL,'Hadir','Terlambat',NULL),
(32,'2503001','2026-09-07 08:04:23',NULL,'Hadir','Terlambat',NULL),
(33,'2602004','2026-09-07 08:04:31',NULL,'Hadir','Terlambat',NULL),
(34,'2506002','2026-09-07 08:04:34',NULL,'Hadir','Terlambat',NULL),
(35,'2306001','2026-09-07 08:04:50',NULL,'Hadir','Terlambat',NULL),
(36,'2603004','2026-09-07 08:04:54',NULL,'Hadir','Terlambat',NULL),
(37,'2503002','2026-09-07 08:04:57',NULL,'Hadir','Terlambat',NULL),
(38,'2604005','2026-09-07 08:05:00',NULL,'Hadir','Terlambat',NULL),
(39,'2506002','2026-09-10 08:58:54',NULL,'Hadir','Terlambat',NULL),
(40,'2602005','2026-09-10 08:59:20',NULL,'Hadir','Terlambat',NULL),
(41,'2603004','2026-09-10 09:00:35',NULL,'Hadir','Terlambat',NULL),
(42,'2602001','2026-09-10 11:22:41',NULL,'Hadir','Terlambat',NULL),
(43,'2602006','2026-09-10 11:22:48',NULL,'Hadir','Terlambat',NULL),
(44,'2501005','2026-09-10 11:23:23',NULL,'Hadir','Terlambat',NULL),
(45,'2602004','2026-09-10 11:24:21',NULL,'Hadir','Terlambat',NULL);
/*!40000 ALTER TABLE `absensi_guru` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `guru`
--

DROP TABLE IF EXISTS `guru`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `guru` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nip` varchar(20) NOT NULL,
  `rfid_uid` varchar(50) DEFAULT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `foto` varchar(255) DEFAULT 'default.jpg',
  `jabatan` varchar(100) DEFAULT NULL,
  `face_embedding` longtext DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nip` (`nip`),
  UNIQUE KEY `rfid_uid` (`rfid_uid`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `guru`
--

LOCK TABLES `guru` WRITE;
/*!40000 ALTER TABLE `guru` DISABLE KEYS */;
INSERT INTO `guru` VALUES
(1,'1901001','2715154701','Yati Supriati','81226096474','','guru_1901001_1785570767.jpg','Ketua Yayasan',NULL),
(3,'2501002','2718946701','Ditya Dwi Edy S.A','81329320582',NULL,'guru_2501002_1785570816.jpg','Sekretaris Yayasan',NULL),
(4,'2601003','2715369997','Muh. Dulkarnaen','83869868741',NULL,'guru_2601003_1785570816.jpg','Sekretaris Yayasan',NULL),
(5,'2601004','2717544173','Nana Rusmana','82316643691',NULL,'guru_2601004_1785570816.jpg','Bendahara Yayasan',NULL),
(6,'2602001','2719128573','Sri Anjani','85795661324',NULL,'guru_2602001_1785570816.jpg','Kepala Sekolah',NULL),
(7,'2402002','2719243341','Eli Rizkiana','89659998939',NULL,'guru_2402002_1785570816.jpg','UMMI',NULL),
(8,'2502003','2715099069','Fauziyah','89615230082',NULL,'guru_2502003_1785570816.jpg','Koordinator Kurikulum',NULL),
(9,'2602004','2717830349','Kanifah','85848641043',NULL,'guru_2602004_1785570816.jpg','Koordinator Kesiswaan',NULL),
(10,'2602005','2717775821','Wulan Sari','89507080854',NULL,'guru_2602005_1785570816.jpg','Guru',NULL),
(11,'2602006','2716813485','Rantika','87892013370',NULL,'guru_2602006_1785570816.jpg','Guru',NULL),
(12,'2503001','2719327373','Nurul Aeni','89634076898',NULL,'guru_2503001_1785570816.jpg','Kepala Sekolah',NULL),
(13,'2503002','2719093197','Ibnul Mubarok','82262420282',NULL,'guru_2503002_1785570816.jpg','Koordinator UMMI',NULL),
(14,'2603004','2716423565','Yulianti','8382366284',NULL,'guru_2603004_1785570816.jpg','Koordinator Kesiswaan',NULL),
(15,'2504001','2715800893','Nursaidah','89520437374',NULL,'guru_2504001_1785570816.jpg','Koordinator Kurikulum',NULL),
(16,'2604004','3278753917','Siti Amelia Nurul Rahmah','83874442299',NULL,'guru_2604004_1785570816.jpg','Guru',NULL),
(17,'2604005','3279703613','Farha Hidayatullaela','83142267207',NULL,'guru_2604005_1785570816.jpg','Guru',NULL),
(18,'2306001','2717891821','Widiyaningrum','89623300515',NULL,'guru_2306001_1785570816.jpg','Admin/Operator',NULL),
(19,'2506002','2716772333','Nabila Azzahra','81997895708',NULL,'guru_2506002_1785570816.jpg','Bendahara Lembaga',NULL),
(20,'2501005','2716688861','Jihan Fahira','85707060734',NULL,'guru_2501005_1785570816.jpg','Kebersihan',NULL),
(21,'2601006','2715540845','Nurhasanah','83897201024',NULL,'guru_2601006_1785570816.jpg','Penjaga Kantin',NULL);
/*!40000 ALTER TABLE `guru` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `kantin_transaksi`
--

DROP TABLE IF EXISTS `kantin_transaksi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kantin_transaksi` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nis` varchar(20) NOT NULL,
  `tipe` enum('debet','kredit') NOT NULL COMMENT 'debet = belanja, kredit = top up',
  `nominal` int(11) NOT NULL,
  `saldo_awal` int(11) NOT NULL,
  `saldo_akhir` int(11) NOT NULL,
  `keterangan` varchar(255) DEFAULT NULL,
  `waktu` datetime NOT NULL,
  `operator` varchar(100) NOT NULL COMMENT 'username atau nama lengkap kasir/admin',
  PRIMARY KEY (`id`),
  KEY `idx_nis` (`nis`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kantin_transaksi`
--

LOCK TABLES `kantin_transaksi` WRITE;
/*!40000 ALTER TABLE `kantin_transaksi` DISABLE KEYS */;
INSERT INTO `kantin_transaksi` VALUES
(1,'42627.1.001','kredit',10000,0,10000,'0','2026-08-06 21:01:03','Solahudin Al Ayubih, S.Kom, Gr.'),
(2,'42627.1.001','kredit',150000,10000,160000,'0','2026-08-12 09:50:09','Solahudin Al Ayubih, S.Kom, Gr.'),
(3,'22627.1.017','kredit',148000,0,148000,'0','2026-08-12 10:02:56','Solahudin Al Ayubih, S.Kom, Gr.'),
(4,'22627.1.017','debet',8000,148000,140000,'0','2026-08-12 10:03:46','Solahudin Al Ayubih, S.Kom, Gr.'),
(5,'12627.1.003','kredit',1000,0,1000,'0','2026-09-10 08:47:37','Nabila Azzahra');
/*!40000 ALTER TABLE `kantin_transaksi` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `kelas`
--

DROP TABLE IF EXISTS `kelas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kelas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_kelas` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nama_kelas` (`nama_kelas`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kelas`
--

LOCK TABLES `kelas` WRITE;
/*!40000 ALTER TABLE `kelas` DISABLE KEYS */;
INSERT INTO `kelas` VALUES
(1,'MDTU'),
(2,'SDIT'),
(3,'TKQ'),
(4,'TR');
/*!40000 ALTER TABLE `kelas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `kiosk_tokens`
--

DROP TABLE IF EXISTS `kiosk_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kiosk_tokens` (
  `id` int(11) NOT NULL,
  `token` varchar(10) NOT NULL,
  `updated_at` datetime NOT NULL,
  `expires_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kiosk_tokens`
--

LOCK TABLES `kiosk_tokens` WRITE;
/*!40000 ALTER TABLE `kiosk_tokens` DISABLE KEYS */;
INSERT INTO `kiosk_tokens` VALUES
(1,'BKGZHW','2026-08-06 20:59:42','2026-08-06 21:04:42');
/*!40000 ALTER TABLE `kiosk_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `libur_manual`
--

DROP TABLE IF EXISTS `libur_manual`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `libur_manual` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tanggal` date NOT NULL,
  `keterangan` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `libur_manual`
--

LOCK TABLES `libur_manual` WRITE;
/*!40000 ALTER TABLE `libur_manual` DISABLE KEYS */;
/*!40000 ALTER TABLE `libur_manual` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pengaturan`
--

DROP TABLE IF EXISTS `pengaturan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pengaturan` (
  `id` int(11) NOT NULL,
  `nama_sekolah` varchar(100) DEFAULT 'Sekolah Kita',
  `jam_masuk` time DEFAULT '07:00:00',
  `jam_pulang_min` time DEFAULT '12:00:00',
  `mode_absen_pulang` tinyint(4) DEFAULT 1,
  `timezone` varchar(50) DEFAULT 'Asia/Jakarta',
  `libur_pekanan` varchar(100) DEFAULT NULL,
  `wa_mode` tinyint(1) DEFAULT 0,
  `wa_token` varchar(255) DEFAULT 'sJ6yY7KFBVV2V5mqtSsV',
  `tg_bot_token` varchar(255) DEFAULT NULL,
  `pass_hapus` varchar(255) DEFAULT NULL,
  `wa_api_url` varchar(255) DEFAULT 'https://api.fonnte.com/send',
  `pesan_masuk` text DEFAULT NULL,
  `pesan_pulang` text DEFAULT NULL,
  `logo_sekolah` varchar(255) DEFAULT 'logo_default.png',
  `smtp_host` varchar(100) DEFAULT 'smtp.gmail.com',
  `smtp_port` int(11) DEFAULT 587,
  `smtp_user` varchar(100) DEFAULT NULL,
  `smtp_pass` varchar(100) DEFAULT NULL,
  `s1_masuk` time DEFAULT '07:00:00',
  `s1_pulang` time DEFAULT '12:00:00',
  `s2_masuk` time DEFAULT '12:30:00',
  `s2_pulang` time DEFAULT '17:00:00',
  `wajib_pulang` tinyint(1) DEFAULT 1,
  `s3_masuk` time DEFAULT '17:00:00',
  `s3_pulang` time DEFAULT '21:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pengaturan`
--

LOCK TABLES `pengaturan` WRITE;
/*!40000 ALTER TABLE `pengaturan` DISABLE KEYS */;
INSERT INTO `pengaturan` VALUES
(1,'Rumah Quran Temi','08:00:00','13:50:00',0,'Asia/Jakarta','Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',0,'hZabAaGOyqzBomleduAzEPUIkyruxvTypTIyiccHqKgWqWxEPT','8373927632:AAF7i3UnoMjHIBMPo8BagRDn_8LCywO6RL8',NULL,'https://api.sidobe.com/wa/v1','Assalamualaikum. wr.wb. \r\nBpk/Ibu. Menginformasikan Ananda *[nama]* telah hadir di sekolah pukul *[jam]* [telat]. ini pesan otomatis saat ananda absen agar tidak membalas pesan ini.\r\n            ','Assalamualaikum.\r\nBpk/Ibu. Ananda  [nama] telah pulang dari sekolah pada [jam]. ini pesan otomatis saat ananda absen agar tidak membalas pesan ini.','logo_1785512911.png','smtp.gmail.com',587,'kirodevide001@gmail.com','gnry hkvp vgrl etvx','07:00:00','14:30:00','14:00:00','17:30:00',1,'07:00:00','11:00:00');
/*!40000 ALTER TABLE `pengaturan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `siswa`
--

DROP TABLE IF EXISTS `siswa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `siswa` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nis` varchar(20) NOT NULL,
  `rfid_uid` varchar(50) DEFAULT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `no_hp_ortu` varchar(20) DEFAULT NULL,
  `telegram_chat_id` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `foto` varchar(255) DEFAULT 'default.jpg',
  `kelas` varchar(20) DEFAULT NULL,
  `face_descriptor` text DEFAULT NULL,
  `sesi` enum('1','2','3') DEFAULT '1',
  `face_embedding` longtext DEFAULT NULL,
  `saldo` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nis` (`nis`),
  UNIQUE KEY `rfid_uid` (`rfid_uid`)
) ENGINE=InnoDB AUTO_INCREMENT=158 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `siswa`
--

LOCK TABLES `siswa` WRITE;
/*!40000 ALTER TABLE `siswa` DISABLE KEYS */;
INSERT INTO `siswa` VALUES
(1,'22425.1.001','2717565741','ABDURRAHMAN HANIF','6282255886540','','','siswa_mdtu_1_1785547930.jpg','MDTU',NULL,'2',NULL,0),
(2,'22425.1.004','2716982077','AHMAD RASYID AL FUADI','6282145760212','','','siswa_mdtu_2_1785547930.jpg','MDTU',NULL,'2',NULL,0),
(3,'22526.1.002','2717531213','AL AYYUBI SYAFIQ HABIBI','6287763322730','','','siswa_mdtu_3_1785547930.jpg','MDTU',NULL,'2',NULL,0),
(4,'22324.1.004','2716096541','ALEA ZAHRA','6283124929996','','','siswa_mdtu_4_1785547930.jpg','MDTU',NULL,'2',NULL,0),
(5,'22526.1.003','2719168349','ALFARIZQI ABDUL JABBAR','62895413128004','','','siswa_mdtu_5_1785547930.jpg','MDTU',NULL,'2',NULL,0),
(6,'22324.1.008','2715560077','ARSYILA ROMEESA FARZANA','6281284831568','','','siswa_mdtu_6_1785547930.jpg','MDTU',NULL,'2',NULL,0),
(7,'22425.1.023','2718464413','ARYA AIMAR RAJENDRA','6282246246624','','','siswa_mdtu_7_1785547930.jpg','MDTU',NULL,'2',NULL,0),
(8,'22526.1.005','2715802941','AZAM ZAIN HAMIZAN','6287830380090','','','siswa_mdtu_8_1785547931.jpg','MDTU',NULL,'2',NULL,0),
(9,'22425.1.006','2716264829','AZKA ARFADHIA HAMDANI','6282219692492','','','siswa_mdtu_9_1785547931.jpg','MDTU',NULL,'2',NULL,0),
(10,'22425.1.007','2718465421','AZKA RAFLI AUFA MAKSUM','6281324030004','','','siswa_mdtu_10_1785547931.jpg','MDTU',NULL,'2',NULL,0),
(11,'22526.1.027','2717972893','BILAL RAMADHAN','62895321605242','','','siswa_mdtu_11_1785547931.jpg','MDTU',NULL,'2',NULL,0),
(12,'22526.1.007','2718675437','DEANA SYAFA NUR ISLAM','6285353938977','','','siswa_mdtu_12_1785547931.jpg','MDTU',NULL,'2',NULL,0),
(13,'22526.1.008','2715230957','DEVIYANI AGUSTIN','6285819758906','','','siswa_mdtu_13_1785547931.jpg','MDTU',NULL,'2',NULL,0),
(14,'22425.1.008','2717457357','GHEA NATUSHA','6281319171009','','','siswa_mdtu_14_1785547931.jpg','MDTU',NULL,'2',NULL,0),
(15,'22526.1.012','2718291085','HAFIDZOH AGHNIA KHANSA','6282298607490','','','siswa_mdtu_15_1785547931.jpg','MDTU',NULL,'2',NULL,0),
(16,'22526.1.013','2719370989','HAFLAH HAFIDZAN AL HIDAYAT','6283154610562','','','siswa_mdtu_16_1785547931.jpg','MDTU',NULL,'2',NULL,0),
(17,'22425.1.009','2719245933','HILYA NAFISAH','628212047253','','','siswa_mdtu_17_1785547931.jpg','MDTU',NULL,'2',NULL,0),
(18,'22425.1.010','2718566445','IRFAN FADHI','6285222530691','','','siswa_mdtu_18_1785547931.jpg','MDTU',NULL,'2',NULL,0),
(19,'22324.1.018','2718383213','KAZEN SHABIL ALDEBARAN','6287727020568','','','siswa_mdtu_19_1785547931.jpg','MDTU',NULL,'2',NULL,0),
(20,'22324.1.011','2715506077','MARYAM','62895807041919','','','siswa_mdtu_20_1785547931.jpg','MDTU',NULL,'2',NULL,0),
(21,'22425.1.011','2716524189','MAURA ELVIRA SYAUQIA','6289515040972','','','siswa_mdtu_21_1785547931.jpg','MDTU',NULL,'2',NULL,0),
(22,'22526.1.015','2718755709','MEIGA PUTRI HANEDI','6281311127679','','','siswa_mdtu_22_1785547931.jpg','MDTU',NULL,'2',NULL,0),
(23,'22526.1.016','2716776749','MOHAMAD YUUKI PRADIPTA','6282311770341','','','siswa_mdtu_23_1785547931.jpg','MDTU',NULL,'2',NULL,0),
(24,'22526.1.018','2715995933','MUHAMMAD AZKA AL FA\'IZ','6287729264924','','','siswa_mdtu_24_1785547931.jpg','MDTU',NULL,'2',NULL,0),
(25,'22526.1.020','2717241469','MUHAMMAD DAFFA ADDIEN','6281313347986','','','siswa_mdtu_25_1785547931.jpg','MDTU',NULL,'2',NULL,0),
(26,'22526.1.021','2718471997','MUHAMMAD FAHRI NURHAKIM','6281322313698','','','siswa_mdtu_26_1785547931.jpg','MDTU',NULL,'2',NULL,0),
(27,'22324.1.014','2716057453','MUHAMMAD HAFIZH TRIYANT ANARGYA','6282120806947','','','siswa_mdtu_27_1785547931.jpg','MDTU',NULL,'2',NULL,0),
(28,'22425.1.013','3279322189','MUHAMMAD NURDAFFA AKBAR','6282128512167','','','siswa_mdtu_28_1785547931.jpg','MDTU',NULL,'2',NULL,0),
(29,'22425.1.014','3277960269','MUHAMMAD RIDWAN SAFI\'I','6289647287610','','','siswa_mdtu_29_1785547931.jpg','MDTU',NULL,'2',NULL,0),
(30,'22425.1.015','3281176509','MUHAMMAD YAZID AL HADROMI','6282218578119','','','2425.1.015_1785548107.jpg','MDTU',NULL,'2',NULL,0),
(31,'22324.1.013','3279566989','NAJMATU ZHOHIRO PUTRI PRAYITNO','62895705125552','','','siswa_mdtu_31_1785547932.jpg','MDTU',NULL,'2',NULL,0),
(32,'22324.1.016','3279932445','NINO RIZKY ALFATHIR','628995777112','','','siswa_mdtu_32_1785547932.jpg','MDTU',NULL,'2',NULL,0),
(33,'22526.1.023','1234567890','RATU BILQIS AZZAHRA','6282220225505','','','siswa_mdtu_33_1785547932.jpg','MDTU',NULL,'2',NULL,0),
(34,'22526.1.024','3280745181','REYHAN TRIYADI','6287777947585','','','siswa_mdtu_34_1785547932.jpg','MDTU',NULL,'2',NULL,0),
(35,'22425.1.018','2716158909','SABIYA ALFATHUNNISSA','6285624471699','','','siswa_mdtu_35_1785547932.jpg','MDTU',NULL,'2',NULL,0),
(36,'22324.1.010','2717116509','SYIFA DWI TIFANI','6285838254944','','','siswa_mdtu_36_1785547932.jpg','MDTU',NULL,'2',NULL,0),
(37,'22526.1.026','2717314701','SYIFA NURUL HASANAH','6281298022414','','','siswa_mdtu_37_1785547932.jpg','MDTU',NULL,'2',NULL,0),
(38,'22425.1.020','2719482733','TRISTAN PRATAMA RIZQULLAH','6289528603392','','','siswa_mdtu_38_1785547932.jpg','MDTU',NULL,'2',NULL,0),
(39,'22425.1.022','2716968013','WISNU ADI KESUMA','6282120855223','','','siswa_mdtu_39_1785547932.jpg','MDTU',NULL,'2',NULL,0),
(40,'22324.1.017','2716758365','ZAKIYYA TALITA SAKHI HABIBI','6287707060734','','','siswa_mdtu_40_1785547932.jpg','MDTU',NULL,'2',NULL,0),
(41,'22627.1.001','2718258445','ALMEIRA BENAZIR MANAF','62895385313008','','','siswa_mdtu_41_1785547930.jpg','MDTU',NULL,'2',NULL,0),
(42,'22627.1.002','2716469853','AXCELLO RAFIANDRA SYAHPUTRA','6283804679509','','','siswa_mdtu_42_1785547930.jpg','MDTU',NULL,'2',NULL,0),
(43,'22627.1.003','2716801293','AYSILA HUSNA','6285352876410','','','siswa_mdtu_43_1785547930.jpg','MDTU',NULL,'2',NULL,0),
(44,'22627.1.004','2717773085','CAHAYA INDAH DARA','6285781125668','','','siswa_mdtu_44_1785547931.jpg','MDTU',NULL,'2',NULL,0),
(45,'22627.1.005','2717640285','GHOSSANY MIR\'ATUL IZZAH','6285224070095','','','siswa_mdtu_45_1785547931.jpg','MDTU',NULL,'2',NULL,0),
(46,'22627.1.006','2715191613','HAFIZHATUL HASNA PUTRI ADELIA','6287828610177','','','siswa_mdtu_46_1785547931.jpg','MDTU',NULL,'2',NULL,0),
(47,'22627.1.007','2717131757','HERZA KENZO ALVIANO','6287720710953','','','siswa_mdtu_47_1785547931.jpg','MDTU',NULL,'2',NULL,0),
(48,'22627.1.008','2716635069','KEIKO RAFIQ ARSYAD','6282240185774','','','siswa_mdtu_48_1785547931.jpg','MDTU',NULL,'2',NULL,0),
(49,'22627.1.009','2716951837','KIRANA AQILLA','6285199229214','','','siswa_mdtu_49_1785547931.jpg','MDTU',NULL,'2',NULL,0),
(50,'22627.1.010','2716386989','MALIQ AL RASYID','6289529449085','','','2627.1.010_1785548076.jpg','MDTU',NULL,'2',NULL,0),
(51,'22627.1.011','2716026477','MAULIDA ZULFA ELZUHARA','628121459427','','','siswa_mdtu_51_1785547931.jpg','MDTU',NULL,'2',NULL,0),
(52,'22627.1.012','2717455229','MOH. AFFSAN SAPUTRA','6287894263121','','','siswa_mdtu_52_1785547931.jpg','MDTU',NULL,'2',NULL,0),
(53,'22627.1.013','3279616237','MUSYRIF MUHAMMAD RIZQIANSYAH','6289661061526','','','siswa_mdtu_53_1785547931.jpg','MDTU',NULL,'2',NULL,0),
(54,'22627.1.014','3281686829','NARESWARA PUTRA BAHARI','6282320257930','','','siswa_mdtu_54_1785547932.jpg','MDTU',NULL,'2',NULL,0),
(55,'22627.1.015','3280955725','NASHA KIREI AZZAHRA','6281564948899','','','siswa_mdtu_55_1785547932.jpg','MDTU',NULL,'2',NULL,0),
(56,'22627.1.016','2716773309','SEFTI MARWAH AZAHRA','628960894005','','','siswa_mdtu_56_1785547932.jpg','MDTU',NULL,'2',NULL,0),
(57,'22627.1.017','2717777533','YASBIH QUUINSYAH SETIAWAN','6289513018292','','','siswa_mdtu_57_1785547932.jpg','MDTU',NULL,'2',NULL,140000),
(58,'22627.1.018','2716968029','ZAFIN AZHARI','6289675812465','','','siswa_mdtu_58_1785547932.jpg','MDTU',NULL,'2',NULL,0),
(59,'22425.1.012','2716843037','MUHAMMAD AZZAM EL-SYAUQI','6282316367755','','','2425.1.012_1785548050.jpg','MDTU',NULL,'2',NULL,0),
(64,'12526.1.001','2719057053','Abil Allbiansyah ','6283825947820','','','siswa_sdit_64_1785555575.jpg','SDIT',NULL,'1',NULL,0),
(65,'12526.1.002','2717850733','Alvino Akmal Nurhidayat','6289824111811','','','siswa_sdit_65_1785555575.jpg','SDIT',NULL,'1',NULL,0),
(66,'12526.1.003','2715486205','Cantika Nur Anindya','6281222741547','','','siswa_sdit_66_1785555575.jpg','SDIT',NULL,'1',NULL,0),
(67,'12526.1.004','2717320141','Fakhroh Noftieleven Faroohah','6285321630432','','','siswa_sdit_67_1785555575.jpg','SDIT',NULL,'1',NULL,0),
(68,'12526.1.005','2718246317','Fanisa Ashalina','6289693837081','','','siswa_sdit_68_1785555576.jpg','SDIT',NULL,'1',NULL,0),
(69,'12526.1.006','2715031501','Hulwah Qonita Raniah','628977058618','','','siswa_sdit_69_1785555576.jpg','SDIT',NULL,'1',NULL,0),
(70,'12526.1.007','2718336461','Muhammad Baba Assamasi','62895377422654','','','siswa_sdit_70_1785555576.jpg','SDIT',NULL,'1',NULL,0),
(71,'12526.1.008','2716454045','Radeya Syairazy Sukmana','6285797487306','','','siswa_sdit_71_1785555576.jpg','SDIT',NULL,'1',NULL,0),
(72,'12627.1.001','2716085901','Adnan Fa\'iz Al Fadhi','6289662202694','','','siswa_sdit_72_1785555575.jpg','SDIT',NULL,'1',NULL,0),
(73,'12627.1.002','2718985501','Aishwa Nayyara Husna','6282145799498','','','siswa_sdit_73_1785555575.jpg','SDIT',NULL,'1',NULL,0),
(74,'12627.1.003','2719130317','Arjuna Fatih Al-Hawwari','6285649029213','','','siswa_sdit_74_1785555575.jpg','SDIT',NULL,'1',NULL,1000),
(75,'12627.1.004','2718237037','Arsyad Alghifari','6287876880589','','','siswa_sdit_75_1785555575.jpg','SDIT',NULL,'1',NULL,0),
(76,'12627.1.005','2718055117','Arumi Nasya Razeeta','6285813943333','','','siswa_sdit_76_1785555575.jpg','SDIT',NULL,'1',NULL,0),
(77,'12627.1.006','2715793005','Dede Khaerul Anam','6285786034640','','','siswa_sdit_77_1785555575.jpg','SDIT',NULL,'1',NULL,0),
(78,'12627.1.007','2717667341','Lusiyana Intan','6282290576498','','','siswa_sdit_78_1785555576.jpg','SDIT',NULL,'1',NULL,0),
(79,'12627.1.008','2717030477','Muhammad Daffa Al-Ghifari','6281214505511','','','siswa_sdit_79_1785555576.jpg','SDIT',NULL,'1',NULL,0),
(80,'12627.1.009','2717096317','Muhammad Rifqi Sujahilman','6282115609442','','','siswa_sdit_80_1785555576.jpg','SDIT',NULL,'1',NULL,0),
(81,'12627.1.010','2716737117','Muhammad Wafiyul Huda','6285743286697','','','siswa_sdit_81_1785555576.jpg','SDIT',NULL,'1',NULL,0),
(82,'12627.1.011','2716251197','Niko Julian Pratama','6282117301795','','','siswa_sdit_82_1785555576.jpg','SDIT',NULL,'1',NULL,0),
(83,'12627.1.012','2718996669','Queensha Arunika Elshanum','628988443150','','','siswa_sdit_83_1785555576.jpg','SDIT',NULL,'1',NULL,0),
(84,'12627.1.013','2716694157','Reza Adinata','6287829251515','','','siswa_sdit_84_1785555576.jpg','SDIT',NULL,'1',NULL,0),
(85,'12627.1.014','2716268045','Riska Tiara Maharani','628999961297','','','siswa_sdit_85_1785555576.jpg','SDIT',NULL,'1',NULL,0),
(86,'12627.1.015','2716640653','Shabrina Azzura Mafazah','6289661717973','','','siswa_sdit_86_1785555576.jpg','SDIT',NULL,'1',NULL,0),
(87,'12627.1.016','2717849901','Shaka Alfarizqi','6281321143593','','','siswa_sdit_87_1785555576.jpg','SDIT',NULL,'1',NULL,0),
(88,'12627.1.017','2716069597','Sultan Raffasya','6281321043058','','','siswa_sdit_88_1785555576.jpg','SDIT',NULL,'1',NULL,0),
(89,'12627.1.018','2716784653','Tania Yoni Putri','6281912998018','','','siswa_sdit_89_1785555576.jpg','SDIT',NULL,'1',NULL,0),
(90,'12627.1.019','2718712717','Ulya Hana Mahdyah','6281322597930','','','siswa_sdit_90_1785555576.jpg','SDIT',NULL,'1',NULL,0),
(91,'32627.1.001','2716807085','ADIBAH SRI RAHAYU','6285320651863','','','siswa_tkq_91_1785568911.jpg','TKQ',NULL,'3',NULL,0),
(92,'32627.1.002','2718024061','AHMAD AL-FATIH','6281324204228','','','siswa_tkq_92_1785568912.jpg','TKQ',NULL,'3',NULL,0),
(93,'32627.1.003','2716229277','AKMAL HAFIDZ AL-DZIKRI','6288211333052','','','siswa_tkq_93_1785568912.jpg','TKQ',NULL,'3',NULL,0),
(94,'32627.1.004','2719128445','ALDEVARO REYHAN NANDANA','6289605527822','','','siswa_tkq_94_1785568912.jpg','TKQ',NULL,'3',NULL,0),
(95,'32627.1.005','2715186557','ALESHA ZAMEENA NASLA','6281288145827','','','siswa_tkq_95_1785568912.jpg','TKQ',NULL,'3',NULL,0),
(96,'32627.1.006','2717177277','ALPIN NURHADI','6285691837487','','','siswa_tkq_96_1785568912.jpg','TKQ',NULL,'3',NULL,0),
(97,'32627.1.007','2717778813','ALTAN ALVARENDRA PRAYITNO','62895705125552','','','siswa_tkq_97_1785568912.jpg','TKQ',NULL,'3',NULL,0),
(98,'32627.1.008','3277501069','ANANTA PRATAMA','6282125158386','','','siswa_tkq_98_1785568912.jpg','TKQ',NULL,'3',NULL,0),
(99,'32627.1.009','3280993373','ARBAIN IZZAN ANNAWAWI','6281316421078','','','siswa_tkq_99_1785568912.jpg','TKQ',NULL,'3',NULL,0),
(100,'32627.1.010','3281754445','ARRAYA QIANDRA AUDREANA','6281321143593','','','siswa_tkq_100_1785568912.jpg','TKQ',NULL,'3',NULL,0),
(101,'32627.1.011','3278097997','ARSAKHA HIROSHI','6282311770341','','','siswa_tkq_101_1785568912.jpg','TKQ',NULL,'3',NULL,0),
(102,'32627.1.012','3278575885','ARZU DWI SYAQILLA','6282129521705','','','siswa_tkq_102_1785568912.jpg','TKQ',NULL,'3',NULL,0),
(103,'32627.1.013','3277435693','AZKIA ZAHIDA NURFAUZIAH','62896969399000','','','siswa_tkq_103_1785568912.jpg','TKQ',NULL,'3',NULL,0),
(104,'32627.1.014','3279379117','CYRINDA BELLA','6282123974326','','','siswa_tkq_104_1785568912.jpg','TKQ',NULL,'3',NULL,0),
(105,'32627.1.015','2715274637','DANEEN GIANI HUTAMA','628996727724','','','siswa_tkq_105_1785568912.jpg','TKQ',NULL,'3',NULL,0),
(106,'32627.1.016','2717399037','DANENDRA DWI PRANATA','628211311505','','','siswa_tkq_106_1785568912.jpg','TKQ',NULL,'3',NULL,0),
(107,'32627.1.017','2717114637','FARKHA AMIELIA','62895357033555','','','siswa_tkq_107_1785568912.jpg','TKQ',NULL,'3',NULL,0),
(108,'32627.1.018','2719034413','FATIMAH DZIHNI HASANAH','628975489228','','','siswa_tkq_108_1785568912.jpg','TKQ',NULL,'3',NULL,0),
(109,'32627.1.019','2719104781','GIBRAN MALIK ALFARIZKY','6289661173587','','','siswa_tkq_109_1785568912.jpg','TKQ',NULL,'3',NULL,0),
(110,'32627.1.020','2718054909','GWEEN ALETA PUTRI SAMPURNA','6281224345982','','','siswa_tkq_110_1785568912.jpg','TKQ',NULL,'3',NULL,0),
(111,'32627.1.021','2716552557','IBRAHIM MISHARY AHZA NASUTION','6282289594616','','','siswa_tkq_111_1785568912.jpg','TKQ',NULL,'3',NULL,0),
(112,'32627.1.022','2715484973','ISMATUL INAYAH','6282315917102','','','siswa_tkq_112_1785568912.jpg','TKQ',NULL,'3',NULL,0),
(113,'32627.1.023','2718581213','KAHAYANG GI AMRILA ALULA','6289530053006','','','siswa_tkq_113_1785568912.jpg','TKQ',NULL,'3',NULL,0),
(114,'32627.1.024','2718433549','KHAIRA HUMAIRA AFSHEENA','6282315793539','','','siswa_tkq_114_1785568912.jpg','TKQ',NULL,'3',NULL,0),
(115,'32627.1.025','2717063885','MEIKHA ADRIANA','6283869513184','','','siswa_tkq_115_1785568913.jpg','TKQ',NULL,'3',NULL,0),
(116,'32627.1.026','2716295613','MUHAMMAD AL FATIH HABIBI','6287763322730','','','siswa_tkq_116_1785568913.jpg','TKQ',NULL,'3',NULL,0),
(117,'32627.1.027','2716633597','MUHAMMAD AL-FATIH ROISUL ISLAM','6289523851944','','','siswa_tkq_117_1785568913.jpg','TKQ',NULL,'3',NULL,0),
(118,'32627.1.028','2716007709','MUHAMMAD ATHARIZZ CALIEF','6281188055966','','','siswa_tkq_118_1785568913.jpg','TKQ',NULL,'3',NULL,0),
(119,'32627.1.029','2717013629','MUHAMMAD AZKA GHIFARI','6282115797787','','','siswa_tkq_119_1785568913.jpg','TKQ',NULL,'3',NULL,0),
(120,'32627.1.030','2716028013','MUHAMMAD SYAUQI RAMADHAN','62895337992391','','','siswa_tkq_120_1785568913.jpg','TKQ',NULL,'3',NULL,0),
(121,'32627.1.031','2717451517','NANDITO','6289662202694','','','siswa_tkq_121_1785568913.jpg','TKQ',NULL,'3',NULL,0),
(122,'32627.1.032','2716566141','RAFA ALFARIZKI','6282129521705','','','siswa_tkq_122_1785568913.jpg','TKQ',NULL,'3',NULL,0),
(123,'32627.1.033','2716840765','REYHAN ALFA RISQI','6287717648645','','','siswa_tkq_123_1785568913.jpg','TKQ',NULL,'3',NULL,0),
(124,'32627.1.034','2716702205','SABRINA KHAIRA NAFISHA','6285624471699','','','siswa_tkq_124_1785568913.jpg','TKQ',NULL,'3',NULL,0),
(125,'32627.1.035','2718833965','SAYHAN FAIZ MUBARAK','6281284831568','','','siswa_tkq_125_1785568913.jpg','TKQ',NULL,'3',NULL,0),
(126,'32627.1.036','2718301021','SYAFIQ ABDURRAHMAN','6283823231318','','','siswa_tkq_126_1785568913.jpg','TKQ',NULL,'3',NULL,0),
(127,'32627.1.037','2716005021','TIARA NUR ROHMAN','62895635869385','','','siswa_tkq_127_1785568913.jpg','TKQ',NULL,'3',NULL,0),
(128,'32627.1.038','2715357341','UGI LIONAFAH','6283894507370','','','siswa_tkq_128_1785568913.jpg','TKQ',NULL,'3',NULL,0),
(129,'32627.1.039','2715721901','UMAR AS-SIDIQ WINATA','6281272235795','','','siswa_tkq_129_1785568913.jpg','TKQ',NULL,'3',NULL,0),
(130,'32627.1.040','2715974797','ZADA BADRA GUMELAR','6287848267592','','','siswa_tkq_130_1785568913.jpg','TKQ',NULL,'3',NULL,0),
(131,'32627.1.041','2719444877','ZALINA HUMAIRA','6281318000034','','','siswa_tkq_131_1785568913.jpg','TKQ',NULL,'3',NULL,0),
(132,'32526.1.001','2718364909','ABDURRAHMAN IHSAN','6282255886540','','','siswa_tkq_132_1785568911.jpg','TKQ',NULL,'3',NULL,0),
(133,'32526.1.002','2715470221','ABHINAYA HENPRI CAHYO ANUGRAH','6283178714498','','','siswa_tkq_133_1785568911.jpg','TKQ',NULL,'3',NULL,0),
(134,'32526.1.004','2715831677','AISYAH SIFA ALINA ROHMAN','6282130215966','','','siswa_tkq_134_1785568912.jpg','TKQ',NULL,'3',NULL,0),
(135,'32526.1.005','2717781597','AKMAL HAQI AMRULLAH','6287717685479','','','siswa_tkq_135_1785568912.jpg','TKQ',NULL,'3',NULL,0),
(136,'32526.1.006','2715285837','ALLEXANDRIA SALSA SABILLAH','6287736446444','','','siswa_tkq_136_1785568912.jpg','TKQ',NULL,'3',NULL,0),
(137,'32526.1.007','3279116813','ARSYA RAHMAN ARRASYIED','6281324541574','','','siswa_tkq_137_1785568912.jpg','TKQ',NULL,'3',NULL,0),
(138,'32526.1.011','2719125533','GAIATRI DAMARA HAYU','6287722425099','','','siswa_tkq_138_1785568912.jpg','TKQ',NULL,'3',NULL,0),
(139,'32425.01.019','2716092173','HANA IMROATUZZAKIYYAH','6289610079122','','','siswa_tkq_139_1785568912.jpg','TKQ',NULL,'3',NULL,0),
(140,'32526.1.013','2715967821','LADY MIKAYLA YABANI','6282317814845','','','siswa_tkq_140_1785568912.jpg','TKQ',NULL,'3',NULL,0),
(141,'32526.1.015','2717064413','LUTHFAN ANKA RAMADHAN','6281220061455','','','siswa_tkq_141_1785568912.jpg','TKQ',NULL,'3',NULL,0),
(142,'32526.1.016','2719160125','MUHAMMAD NAZRIL BILLAR SETIAWAN','6281222158153','','','siswa_tkq_142_1785568913.jpg','TKQ',NULL,'3',NULL,0),
(143,'32526.1.017','2715375037','MUHAMMAD REYNAND AL LUTHFI','6285813943333','','','siswa_tkq_143_1785568913.jpg','TKQ',NULL,'3',NULL,0),
(144,'32526.1.018','2716367853','MUHAMMAD ZAYDAN BADAWI','6282217486552','','','siswa_tkq_144_1785568913.jpg','TKQ',NULL,'3',NULL,0),
(145,'32526.1.020','2716418669','MUTIARA KAYREEN SUNANDAR','6285864128915','','','siswa_tkq_145_1785568913.jpg','TKQ',NULL,'3',NULL,0),
(146,'32526.1.021','2718240749','SHAHIA SIHFA HASRI','62811201439','','','siswa_tkq_146_1785568913.jpg','TKQ',NULL,'3',NULL,0),
(147,'32526.1.022','2718557245','SHAKIRA KAHISA SAFWANA','6287828959857','','','siswa_tkq_147_1785568913.jpg','TKQ',NULL,'3',NULL,0),
(148,'32526.1.023','2717410317','SITI MELY YANI','6282321077335','','','siswa_tkq_148_1785568913.jpg','TKQ',NULL,'3',NULL,0),
(149,'32526.1.024','2715798829','SITI NUR HASANAH','6283871201271','','','siswa_tkq_149_1785568913.jpg','TKQ',NULL,'3',NULL,0),
(150,'32526.1.025','2716716157','SYAKIRA FAHIRA RAMADHANI','6281386181607','','','siswa_tkq_150_1785568913.jpg','TKQ',NULL,'3',NULL,0),
(151,'42526.1.001','2718685405','Ayatul Husna Adelia Putri','6287828610177',NULL,NULL,'siswa_tr_425261001_1785571531.jpg','TR',NULL,'1',NULL,0),
(152,'42425.01.00','2718226141','Fiorenza Amanda','6282127941204',NULL,NULL,'siswa_tr_424250100_1785571531.jpg','TR',NULL,'1',NULL,0),
(153,'42526.1.006','2715514477','Siti Vera Fitriyani','6282118108761',NULL,NULL,'siswa_tr_425261006_1785571531.jpg','TR',NULL,'1',NULL,0),
(154,'42627.1.001','2717439277','Abdul Naufal Alhadid','6285295866584',NULL,NULL,'siswa_tr_426271001_1785571531.jpg','TR',NULL,'1',NULL,160000),
(155,'42627.1.002','2715941709','Alsafani','6287876880589',NULL,NULL,'siswa_tr_426271002_1785571531.jpg','TR',NULL,'1',NULL,0),
(156,'42627.1.003','2718245885','Elvira Balqis Ali','6282319800894',NULL,NULL,'siswa_tr_426271003_1785571531.jpg','TR',NULL,'1',NULL,0),
(157,'42627.1.004','2717057229','Zahira Hanifah Liljannah','6282116201490',NULL,NULL,'siswa_tr_426271004_1785571531.jpg','TR',NULL,'1',NULL,0);
/*!40000 ALTER TABLE `siswa` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `spp_jenis_tagihan`
--

DROP TABLE IF EXISTS `spp_jenis_tagihan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `spp_jenis_tagihan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `nominal` decimal(12,2) NOT NULL,
  `tahun_ajaran` varchar(20) NOT NULL,
  `jatuh_tempo` date NOT NULL,
  `berlaku_untuk` varchar(50) DEFAULT 'Semua',
  `aktif` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `spp_jenis_tagihan`
--

LOCK TABLES `spp_jenis_tagihan` WRITE;
/*!40000 ALTER TABLE `spp_jenis_tagihan` DISABLE KEYS */;
INSERT INTO `spp_jenis_tagihan` VALUES
(2,'Praktek IPA',100000.00,'2025/2026','2026-08-11','MDTU',1,'2026-08-04 16:35:35'),
(3,'SPP Bulan September',350000.00,'2025/2026','2026-09-16','MDTU',1,'2026-08-04 16:51:36');
/*!40000 ALTER TABLE `spp_jenis_tagihan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `spp_log_notifikasi`
--

DROP TABLE IF EXISTS `spp_log_notifikasi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `spp_log_notifikasi` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tagihan_id` int(11) NOT NULL,
  `no_hp` varchar(20) NOT NULL,
  `pesan` text NOT NULL,
  `status` varchar(20) DEFAULT 'Terkirim',
  `sent_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `tagihan_id` (`tagihan_id`)
) ENGINE=InnoDB AUTO_INCREMENT=119 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `spp_log_notifikasi`
--

LOCK TABLES `spp_log_notifikasi` WRITE;
/*!40000 ALTER TABLE `spp_log_notifikasi` DISABLE KEYS */;
INSERT INTO `spp_log_notifikasi` VALUES
(1,1,'6282255886540','Bismillah, Yth. Orang Tua dari ABDURRAHMAN HANIF (MDTU).\n\nTagihan SPP Praktek IPA telah diterbitkan sebesar *Rp 100.000* dengan jatuh tempo tanggal *11 Aug 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=57463c81c1e420c920f08a774e849ad7bed9f6a70cc9e8fcfa07ee324ba77f18\n\nTerima Kasih.','Terkirim','2026-08-04 16:40:06'),
(2,2,'6282145760212','Bismillah, Yth. Orang Tua dari AHMAD RASYID AL FUADI (MDTU).\n\nTagihan SPP Praktek IPA telah diterbitkan sebesar *Rp 100.000* dengan jatuh tempo tanggal *11 Aug 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=fd14d89a55d855a29195bd2227426718c07a1e91087ec9be0ee5491f5751f8ba\n\nTerima Kasih.','Terkirim','2026-08-04 16:40:06'),
(3,3,'6287763322730','Bismillah, Yth. Orang Tua dari AL AYYUBI SYAFIQ HABIBI (MDTU).\n\nTagihan SPP Praktek IPA telah diterbitkan sebesar *Rp 100.000* dengan jatuh tempo tanggal *11 Aug 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=36c2713c8b63c7d6905cada87f6c1395dc93279fcf0bdb888d096f189ec6846d\n\nTerima Kasih.','Terkirim','2026-08-04 16:40:06'),
(4,4,'6283124929996','Bismillah, Yth. Orang Tua dari ALEA ZAHRA (MDTU).\n\nTagihan SPP Praktek IPA telah diterbitkan sebesar *Rp 100.000* dengan jatuh tempo tanggal *11 Aug 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=e6b8f3a9182c0b1e69549bfb58b86708d9e6b882aaec41c6cc08c45c768afd89\n\nTerima Kasih.','Terkirim','2026-08-04 16:40:06'),
(5,5,'62895413128004','Bismillah, Yth. Orang Tua dari ALFARIZQI ABDUL JABBAR (MDTU).\n\nTagihan SPP Praktek IPA telah diterbitkan sebesar *Rp 100.000* dengan jatuh tempo tanggal *11 Aug 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=393c83a010b98673944b3befcce1fe51c9967fb394dc620deb647d973de8d46f\n\nTerima Kasih.','Terkirim','2026-08-04 16:40:06'),
(6,6,'6281284831568','Bismillah, Yth. Orang Tua dari ARSYILA ROMEESA FARZANA (MDTU).\n\nTagihan SPP Praktek IPA telah diterbitkan sebesar *Rp 100.000* dengan jatuh tempo tanggal *11 Aug 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=c128e3a620b3777af6cdb5abd96373581ca26582bc67369b4b605956fb91555b\n\nTerima Kasih.','Terkirim','2026-08-04 16:40:06'),
(7,7,'6282246246624','Bismillah, Yth. Orang Tua dari ARYA AIMAR RAJENDRA (MDTU).\n\nTagihan SPP Praktek IPA telah diterbitkan sebesar *Rp 100.000* dengan jatuh tempo tanggal *11 Aug 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=4c88313dc14d331ae92a73f37d3fa7ee8ec1bc37bfa2263d84eba549a5cf0778\n\nTerima Kasih.','Terkirim','2026-08-04 16:40:06'),
(8,8,'6287830380090','Bismillah, Yth. Orang Tua dari AZAM ZAIN HAMIZAN (MDTU).\n\nTagihan SPP Praktek IPA telah diterbitkan sebesar *Rp 100.000* dengan jatuh tempo tanggal *11 Aug 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=91db6214cfbebc78c2663581d76631d5acde8f4da8f00e957372960100fd6e79\n\nTerima Kasih.','Terkirim','2026-08-04 16:40:06'),
(9,9,'6282219692492','Bismillah, Yth. Orang Tua dari AZKA ARFADHIA HAMDANI (MDTU).\n\nTagihan SPP Praktek IPA telah diterbitkan sebesar *Rp 100.000* dengan jatuh tempo tanggal *11 Aug 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=bdb4025999c82fe0228b0b96e9f381baf31164ea1d1329e7df30e39ed4d84212\n\nTerima Kasih.','Terkirim','2026-08-04 16:40:06'),
(10,10,'6281324030004','Bismillah, Yth. Orang Tua dari AZKA RAFLI AUFA MAKSUM (MDTU).\n\nTagihan SPP Praktek IPA telah diterbitkan sebesar *Rp 100.000* dengan jatuh tempo tanggal *11 Aug 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=a5b102948edbbeeb5e92ef74a002d7bf16edadb46b60eb4ad8f5d09ba4335540\n\nTerima Kasih.','Terkirim','2026-08-04 16:40:06'),
(11,11,'62895321605242','Bismillah, Yth. Orang Tua dari BILAL RAMADHAN (MDTU).\n\nTagihan SPP Praktek IPA telah diterbitkan sebesar *Rp 100.000* dengan jatuh tempo tanggal *11 Aug 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=aebb96c7eeedae91dac89f3b68074622c6c4d05a46293065166df70a1538eb8b\n\nTerima Kasih.','Terkirim','2026-08-04 16:40:06'),
(12,12,'6285353938977','Bismillah, Yth. Orang Tua dari DEANA SYAFA NUR ISLAM (MDTU).\n\nTagihan SPP Praktek IPA telah diterbitkan sebesar *Rp 100.000* dengan jatuh tempo tanggal *11 Aug 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=cbc3634432fe70f567d97db6b608b1626cda470c1f97f89639b75855ce970ed8\n\nTerima Kasih.','Terkirim','2026-08-04 16:40:06'),
(13,13,'6285819758906','Bismillah, Yth. Orang Tua dari DEVIYANI AGUSTIN (MDTU).\n\nTagihan SPP Praktek IPA telah diterbitkan sebesar *Rp 100.000* dengan jatuh tempo tanggal *11 Aug 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=a1de65f497ba9b572071ce6d4e60aaed646f0479c99d675435c6ba6f41516846\n\nTerima Kasih.','Terkirim','2026-08-04 16:40:06'),
(14,14,'6281319171009','Bismillah, Yth. Orang Tua dari GHEA NATUSHA (MDTU).\n\nTagihan SPP Praktek IPA telah diterbitkan sebesar *Rp 100.000* dengan jatuh tempo tanggal *11 Aug 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=6c4bacb6811f9da3c645c457f34625510004f7ae7cbbd8d8ec973de0d055f82d\n\nTerima Kasih.','Terkirim','2026-08-04 16:40:06'),
(15,15,'6282298607490','Bismillah, Yth. Orang Tua dari HAFIDZOH AGHNIA KHANSA (MDTU).\n\nTagihan SPP Praktek IPA telah diterbitkan sebesar *Rp 100.000* dengan jatuh tempo tanggal *11 Aug 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=13c54a60056db0b62c84c1cdb8913d2ba3dd51a335292d82d428fd5759c520e2\n\nTerima Kasih.','Terkirim','2026-08-04 16:40:06'),
(16,16,'6283154610562','Bismillah, Yth. Orang Tua dari HAFLAH HAFIDZAN AL HIDAYAT (MDTU).\n\nTagihan SPP Praktek IPA telah diterbitkan sebesar *Rp 100.000* dengan jatuh tempo tanggal *11 Aug 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=0b51dc13ffbd6f4b377eb774e7d4b3843e92c65998ddaaf5b82e424e784075e7\n\nTerima Kasih.','Terkirim','2026-08-04 16:40:06'),
(17,17,'628212047253','Bismillah, Yth. Orang Tua dari HILYA NAFISAH (MDTU).\n\nTagihan SPP Praktek IPA telah diterbitkan sebesar *Rp 100.000* dengan jatuh tempo tanggal *11 Aug 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=2031fcb74def6eaa5cfb62df5edc621fdc96a020d0bd6b8731249dcfd979aa6d\n\nTerima Kasih.','Terkirim','2026-08-04 16:40:06'),
(18,18,'6285222530691','Bismillah, Yth. Orang Tua dari IRFAN FADHI (MDTU).\n\nTagihan SPP Praktek IPA telah diterbitkan sebesar *Rp 100.000* dengan jatuh tempo tanggal *11 Aug 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=92e170290856e7fdae2fd6f7187a9eb7b4754059b10c63cd4607c7ce39a5019f\n\nTerima Kasih.','Terkirim','2026-08-04 16:40:06'),
(19,19,'6287727020568','Bismillah, Yth. Orang Tua dari KAZEN SHABIL ALDEBARAN (MDTU).\n\nTagihan SPP Praktek IPA telah diterbitkan sebesar *Rp 100.000* dengan jatuh tempo tanggal *11 Aug 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=e1f44ac23fe65fe521cf7a25549da8c911c395fa53c6f32814da4d81c8fb6528\n\nTerima Kasih.','Terkirim','2026-08-04 16:40:06'),
(20,20,'62895807041919','Bismillah, Yth. Orang Tua dari MARYAM (MDTU).\n\nTagihan SPP Praktek IPA telah diterbitkan sebesar *Rp 100.000* dengan jatuh tempo tanggal *11 Aug 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=94ec9c76f2dfa5f7174f3feb81d88d940a9bf5946862c2c80d91cbfe364d353a\n\nTerima Kasih.','Terkirim','2026-08-04 16:40:06'),
(21,21,'6289515040972','Bismillah, Yth. Orang Tua dari MAURA ELVIRA SYAUQIA (MDTU).\n\nTagihan SPP Praktek IPA telah diterbitkan sebesar *Rp 100.000* dengan jatuh tempo tanggal *11 Aug 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=3a2193d3306ff4cd6fa1d92887ffe4f9455ac7dd8849e9d1c157e8ae3ce1974c\n\nTerima Kasih.','Terkirim','2026-08-04 16:40:06'),
(22,22,'6281311127679','Bismillah, Yth. Orang Tua dari MEIGA PUTRI HANEDI (MDTU).\n\nTagihan SPP Praktek IPA telah diterbitkan sebesar *Rp 100.000* dengan jatuh tempo tanggal *11 Aug 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=5f7eddafc57a79f3e658c936e42ee45485d3608dc2933f5db40d903a7ff9cdac\n\nTerima Kasih.','Terkirim','2026-08-04 16:40:06'),
(23,23,'6282311770341','Bismillah, Yth. Orang Tua dari MOHAMAD YUUKI PRADIPTA (MDTU).\n\nTagihan SPP Praktek IPA telah diterbitkan sebesar *Rp 100.000* dengan jatuh tempo tanggal *11 Aug 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=18466e02a1081c84380d013780322f18a4507bc5e781d58797c6d01081632834\n\nTerima Kasih.','Terkirim','2026-08-04 16:40:06'),
(24,24,'6287729264924','Bismillah, Yth. Orang Tua dari MUHAMMAD AZKA AL FA\'IZ (MDTU).\n\nTagihan SPP Praktek IPA telah diterbitkan sebesar *Rp 100.000* dengan jatuh tempo tanggal *11 Aug 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=1d1ae9e6000a248d495fc314b1f484fa70e9556b46be78c5c8a359e1511d072a\n\nTerima Kasih.','Terkirim','2026-08-04 16:40:06'),
(25,25,'6281313347986','Bismillah, Yth. Orang Tua dari MUHAMMAD DAFFA ADDIEN (MDTU).\n\nTagihan SPP Praktek IPA telah diterbitkan sebesar *Rp 100.000* dengan jatuh tempo tanggal *11 Aug 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=f861797e6cf03a89841a967e709c83fd2ee0516a65ac69ad849cbe12b2e287c7\n\nTerima Kasih.','Terkirim','2026-08-04 16:40:06'),
(26,26,'6281322313698','Bismillah, Yth. Orang Tua dari MUHAMMAD FAHRI NURHAKIM (MDTU).\n\nTagihan SPP Praktek IPA telah diterbitkan sebesar *Rp 100.000* dengan jatuh tempo tanggal *11 Aug 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=f9a26740debe548261c09dac9242579e1610e14b4812cb6feff94f8c38389517\n\nTerima Kasih.','Terkirim','2026-08-04 16:40:06'),
(27,27,'6282120806947','Bismillah, Yth. Orang Tua dari MUHAMMAD HAFIZH TRIYANT ANARGYA (MDTU).\n\nTagihan SPP Praktek IPA telah diterbitkan sebesar *Rp 100.000* dengan jatuh tempo tanggal *11 Aug 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=9c711dcde52199ec227d5cd1f7a51d58eaed1fdd9de978cf0a0a52b86b53c4c7\n\nTerima Kasih.','Terkirim','2026-08-04 16:40:06'),
(28,28,'6282128512167','Bismillah, Yth. Orang Tua dari MUHAMMAD NURDAFFA AKBAR (MDTU).\n\nTagihan SPP Praktek IPA telah diterbitkan sebesar *Rp 100.000* dengan jatuh tempo tanggal *11 Aug 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=f93aef6c85c3e9423b103bce419dfc8958141703b5ee754ccdfa43440ce177d1\n\nTerima Kasih.','Terkirim','2026-08-04 16:40:06'),
(29,29,'6289647287610','Bismillah, Yth. Orang Tua dari MUHAMMAD RIDWAN SAFI\'I (MDTU).\n\nTagihan SPP Praktek IPA telah diterbitkan sebesar *Rp 100.000* dengan jatuh tempo tanggal *11 Aug 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=b765bc1304e3955aa1298d21c9955ecb0b35bcc23ff8f88a3fff82c9d9e72e7b\n\nTerima Kasih.','Terkirim','2026-08-04 16:40:06'),
(30,30,'6282218578119','Bismillah, Yth. Orang Tua dari MUHAMMAD YAZID AL HADROMI (MDTU).\n\nTagihan SPP Praktek IPA telah diterbitkan sebesar *Rp 100.000* dengan jatuh tempo tanggal *11 Aug 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=fb64f8d0bba054a5455e0c13e2e83896af673ec65433a6abc01c0e105b0657e5\n\nTerima Kasih.','Terkirim','2026-08-04 16:40:06'),
(31,31,'62895705125552','Bismillah, Yth. Orang Tua dari NAJMATU ZHOHIRO PUTRI PRAYITNO (MDTU).\n\nTagihan SPP Praktek IPA telah diterbitkan sebesar *Rp 100.000* dengan jatuh tempo tanggal *11 Aug 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=7e2192527038c8768331116cc645d559c9b2cfbab90fe4166012c15da2b53fb9\n\nTerima Kasih.','Terkirim','2026-08-04 16:40:06'),
(32,32,'628995777112','Bismillah, Yth. Orang Tua dari NINO RIZKY ALFATHIR (MDTU).\n\nTagihan SPP Praktek IPA telah diterbitkan sebesar *Rp 100.000* dengan jatuh tempo tanggal *11 Aug 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=cec87fd4532e66971c2d2db05749c56d943c72082a9470e34f04c6be343ca53c\n\nTerima Kasih.','Terkirim','2026-08-04 16:40:06'),
(33,33,'6282220225505','Bismillah, Yth. Orang Tua dari RATU BILQIS AZZAHRA (MDTU).\n\nTagihan SPP Praktek IPA telah diterbitkan sebesar *Rp 100.000* dengan jatuh tempo tanggal *11 Aug 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=b73b6cd9c403bb0ddd9f50c1de2d5cc61058b85c1f09503db103974b988e48b6\n\nTerima Kasih.','Terkirim','2026-08-04 16:40:06'),
(34,34,'6287777947585','Bismillah, Yth. Orang Tua dari REYHAN TRIYADI (MDTU).\n\nTagihan SPP Praktek IPA telah diterbitkan sebesar *Rp 100.000* dengan jatuh tempo tanggal *11 Aug 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=f52165f77bc9b2f88fff5b7163c974d70e31380ebc11c603d45fe2a12b6dc108\n\nTerima Kasih.','Terkirim','2026-08-04 16:40:06'),
(35,35,'6285624471699','Bismillah, Yth. Orang Tua dari SABIYA ALFATHUNNISSA (MDTU).\n\nTagihan SPP Praktek IPA telah diterbitkan sebesar *Rp 100.000* dengan jatuh tempo tanggal *11 Aug 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=7f712fcba45a0f1cb7217007f8764f3051132d422b74b3040fcf86368296dc63\n\nTerima Kasih.','Terkirim','2026-08-04 16:40:06'),
(36,36,'6285838254944','Bismillah, Yth. Orang Tua dari SYIFA DWI TIFANI (MDTU).\n\nTagihan SPP Praktek IPA telah diterbitkan sebesar *Rp 100.000* dengan jatuh tempo tanggal *11 Aug 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=8b211fc7653912ffceaf7ccfea2dad79621a5c7eaca8934e0afa769f86e9fdcd\n\nTerima Kasih.','Terkirim','2026-08-04 16:40:06'),
(37,37,'6281298022414','Bismillah, Yth. Orang Tua dari SYIFA NURUL HASANAH (MDTU).\n\nTagihan SPP Praktek IPA telah diterbitkan sebesar *Rp 100.000* dengan jatuh tempo tanggal *11 Aug 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=7de7e839a45d0eb52672e15095bea4ef7c1b006e7e7cb39f3504932c2616d457\n\nTerima Kasih.','Terkirim','2026-08-04 16:40:06'),
(38,38,'6289528603392','Bismillah, Yth. Orang Tua dari TRISTAN PRATAMA RIZQULLAH (MDTU).\n\nTagihan SPP Praktek IPA telah diterbitkan sebesar *Rp 100.000* dengan jatuh tempo tanggal *11 Aug 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=01b9c497b9376e91162c8af4b5ecb91c3ee59b1d772b4256299fdb60f9297093\n\nTerima Kasih.','Terkirim','2026-08-04 16:40:06'),
(39,39,'6282120855223','Bismillah, Yth. Orang Tua dari WISNU ADI KESUMA (MDTU).\n\nTagihan SPP Praktek IPA telah diterbitkan sebesar *Rp 100.000* dengan jatuh tempo tanggal *11 Aug 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=1d577a02ea90c8a4074cfda01bb0531d6ad847448289ccbf6b5bc811b2752183\n\nTerima Kasih.','Terkirim','2026-08-04 16:40:06'),
(40,40,'6287707060734','Bismillah, Yth. Orang Tua dari ZAKIYYA TALITA SAKHI HABIBI (MDTU).\n\nTagihan SPP Praktek IPA telah diterbitkan sebesar *Rp 100.000* dengan jatuh tempo tanggal *11 Aug 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=98288e5625d1cfbb61bb67890d0fd6116e1467eeb48b7a69f3dd4d1ec37043fd\n\nTerima Kasih.','Terkirim','2026-08-04 16:40:06'),
(41,41,'62895385313008','Bismillah, Yth. Orang Tua dari ALMEIRA BENAZIR MANAF (MDTU).\n\nTagihan SPP Praktek IPA telah diterbitkan sebesar *Rp 100.000* dengan jatuh tempo tanggal *11 Aug 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=7f3743a0f3e3df096b6bde3152a9c562400e52c6f103e99ae287e079864a4128\n\nTerima Kasih.','Terkirim','2026-08-04 16:40:06'),
(42,42,'6283804679509','Bismillah, Yth. Orang Tua dari AXCELLO RAFIANDRA SYAHPUTRA (MDTU).\n\nTagihan SPP Praktek IPA telah diterbitkan sebesar *Rp 100.000* dengan jatuh tempo tanggal *11 Aug 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=cdb6a8c972b82aa22050e63df00a568db4220216da50e3be3081d1f26a1b793f\n\nTerima Kasih.','Terkirim','2026-08-04 16:40:06'),
(43,43,'6285352876410','Bismillah, Yth. Orang Tua dari AYSILA HUSNA (MDTU).\n\nTagihan SPP Praktek IPA telah diterbitkan sebesar *Rp 100.000* dengan jatuh tempo tanggal *11 Aug 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=ad0a82fa8c0b134cfa957d738050d3c223b7bcafc570063c521ccfe1cde10adb\n\nTerima Kasih.','Terkirim','2026-08-04 16:40:06'),
(44,44,'6285781125668','Bismillah, Yth. Orang Tua dari CAHAYA INDAH DARA (MDTU).\n\nTagihan SPP Praktek IPA telah diterbitkan sebesar *Rp 100.000* dengan jatuh tempo tanggal *11 Aug 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=464b4ced2b83fe910797ee34d93c2b119cbf6d1accaca2e44a7605d3ec3b811f\n\nTerima Kasih.','Terkirim','2026-08-04 16:40:06'),
(45,45,'6285224070095','Bismillah, Yth. Orang Tua dari GHOSSANY MIR\'ATUL IZZAH (MDTU).\n\nTagihan SPP Praktek IPA telah diterbitkan sebesar *Rp 100.000* dengan jatuh tempo tanggal *11 Aug 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=f121dc7df2bf91647d0106cacc57b6d21ae05731e1c93ccd6d586b1b04504664\n\nTerima Kasih.','Terkirim','2026-08-04 16:40:06'),
(46,46,'6287828610177','Bismillah, Yth. Orang Tua dari HAFIZHATUL HASNA PUTRI ADELIA (MDTU).\n\nTagihan SPP Praktek IPA telah diterbitkan sebesar *Rp 100.000* dengan jatuh tempo tanggal *11 Aug 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=44d874258148ad1516bbe4d6ed38160e3ae44ac8ceaa53968d70c4da48bb72b7\n\nTerima Kasih.','Terkirim','2026-08-04 16:40:06'),
(47,47,'6287720710953','Bismillah, Yth. Orang Tua dari HERZA KENZO ALVIANO (MDTU).\n\nTagihan SPP Praktek IPA telah diterbitkan sebesar *Rp 100.000* dengan jatuh tempo tanggal *11 Aug 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=a839de47d486cafb38a2c267784febbbb297e43204b18caf5f40698f58aa3ba2\n\nTerima Kasih.','Terkirim','2026-08-04 16:40:06'),
(48,48,'6282240185774','Bismillah, Yth. Orang Tua dari KEIKO RAFIQ ARSYAD (MDTU).\n\nTagihan SPP Praktek IPA telah diterbitkan sebesar *Rp 100.000* dengan jatuh tempo tanggal *11 Aug 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=e434915fc6d7d75925a1048c3f98523530252c17102c40548efd446b9001f37c\n\nTerima Kasih.','Terkirim','2026-08-04 16:40:06'),
(49,49,'6285199229214','Bismillah, Yth. Orang Tua dari KIRANA AQILLA (MDTU).\n\nTagihan SPP Praktek IPA telah diterbitkan sebesar *Rp 100.000* dengan jatuh tempo tanggal *11 Aug 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=760fdf7bb310c983383995c823f74b42211f85969e82764edc2ee3f890ccd691\n\nTerima Kasih.','Terkirim','2026-08-04 16:40:06'),
(50,50,'6289529449085','Bismillah, Yth. Orang Tua dari MALIQ AL RASYID (MDTU).\n\nTagihan SPP Praktek IPA telah diterbitkan sebesar *Rp 100.000* dengan jatuh tempo tanggal *11 Aug 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=6cc0a74e3f82c9a1148a4805e1a6970c4b32bb6db3eb99fb57e200e95a93c7ee\n\nTerima Kasih.','Terkirim','2026-08-04 16:40:06'),
(51,51,'628121459427','Bismillah, Yth. Orang Tua dari MAULIDA ZULFA ELZUHARA (MDTU).\n\nTagihan SPP Praktek IPA telah diterbitkan sebesar *Rp 100.000* dengan jatuh tempo tanggal *11 Aug 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=0983521cd3f7e20b4aa00f766062f3fbce8cd7b9a134017a65d29834873ac34e\n\nTerima Kasih.','Terkirim','2026-08-04 16:40:06'),
(52,52,'6287894263121','Bismillah, Yth. Orang Tua dari MOH. AFFSAN SAPUTRA (MDTU).\n\nTagihan SPP Praktek IPA telah diterbitkan sebesar *Rp 100.000* dengan jatuh tempo tanggal *11 Aug 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=f7d02e02e9bab0d68cb0de0645940d9fd59062ace1a4332d2994d4264b7077c5\n\nTerima Kasih.','Terkirim','2026-08-04 16:40:06'),
(53,53,'6289661061526','Bismillah, Yth. Orang Tua dari MUSYRIF MUHAMMAD RIZQIANSYAH (MDTU).\n\nTagihan SPP Praktek IPA telah diterbitkan sebesar *Rp 100.000* dengan jatuh tempo tanggal *11 Aug 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=5d18401952b26f3d6c5d4566ae72d33659597182e6eb9cf3ae2c59ddd3039a81\n\nTerima Kasih.','Terkirim','2026-08-04 16:40:06'),
(54,54,'6282320257930','Bismillah, Yth. Orang Tua dari NARESWARA PUTRA BAHARI (MDTU).\n\nTagihan SPP Praktek IPA telah diterbitkan sebesar *Rp 100.000* dengan jatuh tempo tanggal *11 Aug 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=375848658696e6a918d903604ccd668df6a9502ab86bbbdb1cb065d65a7669a2\n\nTerima Kasih.','Terkirim','2026-08-04 16:40:06'),
(55,55,'6281564948899','Bismillah, Yth. Orang Tua dari NASHA KIREI AZZAHRA (MDTU).\n\nTagihan SPP Praktek IPA telah diterbitkan sebesar *Rp 100.000* dengan jatuh tempo tanggal *11 Aug 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=e1773776cb67b2549ed116ee2e53cfd12de7877bdbd5f5d3384af2c11f83d5ed\n\nTerima Kasih.','Terkirim','2026-08-04 16:40:06'),
(56,56,'628960894005','Bismillah, Yth. Orang Tua dari SEFTI MARWAH AZAHRA (MDTU).\n\nTagihan SPP Praktek IPA telah diterbitkan sebesar *Rp 100.000* dengan jatuh tempo tanggal *11 Aug 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=6c92b87e4deac753912f9089df94bc9422fb2029d9941b976a67d6065e9c9a16\n\nTerima Kasih.','Terkirim','2026-08-04 16:40:06'),
(57,57,'6289513018292','Bismillah, Yth. Orang Tua dari YASBIH QUUINSYAH SETIAWAN (MDTU).\n\nTagihan SPP Praktek IPA telah diterbitkan sebesar *Rp 100.000* dengan jatuh tempo tanggal *11 Aug 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=42f3e368403033549be665845e9f3a1f212a19a2753b2731c8b00c00c042de1e\n\nTerima Kasih.','Terkirim','2026-08-04 16:40:06'),
(58,58,'6289675812465','Bismillah, Yth. Orang Tua dari ZAFIN AZHARI (MDTU).\n\nTagihan SPP Praktek IPA telah diterbitkan sebesar *Rp 100.000* dengan jatuh tempo tanggal *11 Aug 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=069d06b57dc4fd288f2110ad36d27a90f28b174560ca06f3ab7947f35668bde1\n\nTerima Kasih.','Terkirim','2026-08-04 16:40:06'),
(59,59,'6282316367755','Bismillah, Yth. Orang Tua dari MUHAMMAD AZZAM EL-SYAUQI (MDTU).\n\nTagihan SPP Praktek IPA telah diterbitkan sebesar *Rp 100.000* dengan jatuh tempo tanggal *11 Aug 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=06006e2f7df750a1acb00480ab2cf77ef37f4dddb20f2cf7372c2b4312c86314\n\nTerima Kasih.','Terkirim','2026-08-04 16:40:06'),
(60,60,'6282255886540','Bismillah, Yth. Orang Tua dari ABDURRAHMAN HANIF (MDTU).\n\nTagihan SPP SPP Bulan September telah diterbitkan sebesar *Rp 350.000* dengan jatuh tempo tanggal *16 Sep 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=a26e62b8838fe516c734e75ee0aff39abc13add3a15b9bf0503bf72039b8cce8\n\nTerima Kasih.','Terkirim','2026-08-04 16:52:42'),
(61,61,'6282145760212','Bismillah, Yth. Orang Tua dari AHMAD RASYID AL FUADI (MDTU).\n\nTagihan SPP SPP Bulan September telah diterbitkan sebesar *Rp 350.000* dengan jatuh tempo tanggal *16 Sep 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=5b915b2100a63c6b01ea9deac4ab2f0b2d5fc51c2330950c6ea413b61047d8f6\n\nTerima Kasih.','Terkirim','2026-08-04 16:52:42'),
(62,62,'6287763322730','Bismillah, Yth. Orang Tua dari AL AYYUBI SYAFIQ HABIBI (MDTU).\n\nTagihan SPP SPP Bulan September telah diterbitkan sebesar *Rp 350.000* dengan jatuh tempo tanggal *16 Sep 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=b007b0cac300a2221cdb4ff615654ca67eb5c21e8daaa187fb189d25e0a9e38a\n\nTerima Kasih.','Terkirim','2026-08-04 16:52:42'),
(63,63,'6283124929996','Bismillah, Yth. Orang Tua dari ALEA ZAHRA (MDTU).\n\nTagihan SPP SPP Bulan September telah diterbitkan sebesar *Rp 350.000* dengan jatuh tempo tanggal *16 Sep 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=877eee22e430b032108abaf559e6a3d841206748dfaf8953cb79746ddd41e7e2\n\nTerima Kasih.','Terkirim','2026-08-04 16:52:42'),
(64,64,'62895413128004','Bismillah, Yth. Orang Tua dari ALFARIZQI ABDUL JABBAR (MDTU).\n\nTagihan SPP SPP Bulan September telah diterbitkan sebesar *Rp 350.000* dengan jatuh tempo tanggal *16 Sep 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=b3b3fa0cac10737243a3b67615d7231333793e3cec181d014a04b7676b99d7cb\n\nTerima Kasih.','Terkirim','2026-08-04 16:52:42'),
(65,65,'6281284831568','Bismillah, Yth. Orang Tua dari ARSYILA ROMEESA FARZANA (MDTU).\n\nTagihan SPP SPP Bulan September telah diterbitkan sebesar *Rp 350.000* dengan jatuh tempo tanggal *16 Sep 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=ebc92df940c5ca5d4e830b0e91aae760f6e0b104172266ecb0dcb8c9f65bbbc3\n\nTerima Kasih.','Terkirim','2026-08-04 16:52:42'),
(66,66,'6282246246624','Bismillah, Yth. Orang Tua dari ARYA AIMAR RAJENDRA (MDTU).\n\nTagihan SPP SPP Bulan September telah diterbitkan sebesar *Rp 350.000* dengan jatuh tempo tanggal *16 Sep 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=e98e0c01b904e086a03e28d9bc936d460bf4ba79ae58378bae2abd40e607cc9d\n\nTerima Kasih.','Terkirim','2026-08-04 16:52:42'),
(67,67,'6287830380090','Bismillah, Yth. Orang Tua dari AZAM ZAIN HAMIZAN (MDTU).\n\nTagihan SPP SPP Bulan September telah diterbitkan sebesar *Rp 350.000* dengan jatuh tempo tanggal *16 Sep 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=2dd59add51b50aa9396df27f3eb15a8fe3b515ccc82343e935656f94405562e0\n\nTerima Kasih.','Terkirim','2026-08-04 16:52:42'),
(68,68,'6282219692492','Bismillah, Yth. Orang Tua dari AZKA ARFADHIA HAMDANI (MDTU).\n\nTagihan SPP SPP Bulan September telah diterbitkan sebesar *Rp 350.000* dengan jatuh tempo tanggal *16 Sep 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=7dd79ecf5da156486b1e3f3ed8c979d1f1a5948772221321389022f8c93fe5d4\n\nTerima Kasih.','Terkirim','2026-08-04 16:52:42'),
(69,69,'6281324030004','Bismillah, Yth. Orang Tua dari AZKA RAFLI AUFA MAKSUM (MDTU).\n\nTagihan SPP SPP Bulan September telah diterbitkan sebesar *Rp 350.000* dengan jatuh tempo tanggal *16 Sep 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=64583d8dd29c07997ba8840e17304aca5159547c5fcd5192cd028db9d5027c8c\n\nTerima Kasih.','Terkirim','2026-08-04 16:52:42'),
(70,70,'62895321605242','Bismillah, Yth. Orang Tua dari BILAL RAMADHAN (MDTU).\n\nTagihan SPP SPP Bulan September telah diterbitkan sebesar *Rp 350.000* dengan jatuh tempo tanggal *16 Sep 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=ed5b1d76fe24c515b76f6f7a108aeb96fb41db03fb98449f899dced31be0d24a\n\nTerima Kasih.','Terkirim','2026-08-04 16:52:42'),
(71,71,'6285353938977','Bismillah, Yth. Orang Tua dari DEANA SYAFA NUR ISLAM (MDTU).\n\nTagihan SPP SPP Bulan September telah diterbitkan sebesar *Rp 350.000* dengan jatuh tempo tanggal *16 Sep 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=ddf12a0278879f588875df92f1b30af412c48bf455cc85f998574e7d2825c50a\n\nTerima Kasih.','Terkirim','2026-08-04 16:52:42'),
(72,72,'6285819758906','Bismillah, Yth. Orang Tua dari DEVIYANI AGUSTIN (MDTU).\n\nTagihan SPP SPP Bulan September telah diterbitkan sebesar *Rp 350.000* dengan jatuh tempo tanggal *16 Sep 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=4b0ca12839c57012309c7922a5c2c565888e43ef7bac79c320bdf3d436d85008\n\nTerima Kasih.','Terkirim','2026-08-04 16:52:42'),
(73,73,'6281319171009','Bismillah, Yth. Orang Tua dari GHEA NATUSHA (MDTU).\n\nTagihan SPP SPP Bulan September telah diterbitkan sebesar *Rp 350.000* dengan jatuh tempo tanggal *16 Sep 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=4eb77f582f12f851716d1a8dc619f08728dbb35e101a0f4a3e3dcf3f13269f39\n\nTerima Kasih.','Terkirim','2026-08-04 16:52:42'),
(74,74,'6282298607490','Bismillah, Yth. Orang Tua dari HAFIDZOH AGHNIA KHANSA (MDTU).\n\nTagihan SPP SPP Bulan September telah diterbitkan sebesar *Rp 350.000* dengan jatuh tempo tanggal *16 Sep 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=add818b39e29e8138b3029412201865693bb4818d2aa956a7ccc22ad61359b0d\n\nTerima Kasih.','Terkirim','2026-08-04 16:52:42'),
(75,75,'6283154610562','Bismillah, Yth. Orang Tua dari HAFLAH HAFIDZAN AL HIDAYAT (MDTU).\n\nTagihan SPP SPP Bulan September telah diterbitkan sebesar *Rp 350.000* dengan jatuh tempo tanggal *16 Sep 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=5fe990422f5f30b4838b6e3d5bf2feaeb21141245b00f10ea9c9f98e7d55679c\n\nTerima Kasih.','Terkirim','2026-08-04 16:52:42'),
(76,76,'628212047253','Bismillah, Yth. Orang Tua dari HILYA NAFISAH (MDTU).\n\nTagihan SPP SPP Bulan September telah diterbitkan sebesar *Rp 350.000* dengan jatuh tempo tanggal *16 Sep 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=30502f72772842971ac600819b397e3ceec90c29824947a39d8ae7faa4eef3d8\n\nTerima Kasih.','Terkirim','2026-08-04 16:52:42'),
(77,77,'6285222530691','Bismillah, Yth. Orang Tua dari IRFAN FADHI (MDTU).\n\nTagihan SPP SPP Bulan September telah diterbitkan sebesar *Rp 350.000* dengan jatuh tempo tanggal *16 Sep 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=5213326cebfd4379d655de8f1257fa82c455e56adda0fab25083979ff34705ab\n\nTerima Kasih.','Terkirim','2026-08-04 16:52:42'),
(78,78,'6287727020568','Bismillah, Yth. Orang Tua dari KAZEN SHABIL ALDEBARAN (MDTU).\n\nTagihan SPP SPP Bulan September telah diterbitkan sebesar *Rp 350.000* dengan jatuh tempo tanggal *16 Sep 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=4083d87a0712cbb2cdcfbce4a812d1a24f86ea76a910c06e83c0247d77320d84\n\nTerima Kasih.','Terkirim','2026-08-04 16:52:42'),
(79,79,'62895807041919','Bismillah, Yth. Orang Tua dari MARYAM (MDTU).\n\nTagihan SPP SPP Bulan September telah diterbitkan sebesar *Rp 350.000* dengan jatuh tempo tanggal *16 Sep 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=271231493e7c66088eaadaeb360940995a06e3f744e8aae874a68ed534f94ba4\n\nTerima Kasih.','Terkirim','2026-08-04 16:52:42'),
(80,80,'6289515040972','Bismillah, Yth. Orang Tua dari MAURA ELVIRA SYAUQIA (MDTU).\n\nTagihan SPP SPP Bulan September telah diterbitkan sebesar *Rp 350.000* dengan jatuh tempo tanggal *16 Sep 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=1943f05737e93d8d1a07c6bf7999adf87bb06449d0e064f2e866a5a4e7cc55f4\n\nTerima Kasih.','Terkirim','2026-08-04 16:52:42'),
(81,81,'6281311127679','Bismillah, Yth. Orang Tua dari MEIGA PUTRI HANEDI (MDTU).\n\nTagihan SPP SPP Bulan September telah diterbitkan sebesar *Rp 350.000* dengan jatuh tempo tanggal *16 Sep 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=521b6b237e0637d756e3be971f452c75d858b86edd1f458b38d09eaf64a1231b\n\nTerima Kasih.','Terkirim','2026-08-04 16:52:42'),
(82,82,'6282311770341','Bismillah, Yth. Orang Tua dari MOHAMAD YUUKI PRADIPTA (MDTU).\n\nTagihan SPP SPP Bulan September telah diterbitkan sebesar *Rp 350.000* dengan jatuh tempo tanggal *16 Sep 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=09956e1a0eec54ca9c11f98e1d6038b4eda9a307416708635afb8d32348d5903\n\nTerima Kasih.','Terkirim','2026-08-04 16:52:42'),
(83,83,'6287729264924','Bismillah, Yth. Orang Tua dari MUHAMMAD AZKA AL FA\'IZ (MDTU).\n\nTagihan SPP SPP Bulan September telah diterbitkan sebesar *Rp 350.000* dengan jatuh tempo tanggal *16 Sep 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=74d8e4f736fb47981d9830dcf2e2445ac50b98f68cb1fc0d9caf581459befdb3\n\nTerima Kasih.','Terkirim','2026-08-04 16:52:42'),
(84,84,'6281313347986','Bismillah, Yth. Orang Tua dari MUHAMMAD DAFFA ADDIEN (MDTU).\n\nTagihan SPP SPP Bulan September telah diterbitkan sebesar *Rp 350.000* dengan jatuh tempo tanggal *16 Sep 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=01f061838160ff8d048081c1dbd6e9a29dedf19846af983ef6ed79bd18ce2f54\n\nTerima Kasih.','Terkirim','2026-08-04 16:52:42'),
(85,85,'6281322313698','Bismillah, Yth. Orang Tua dari MUHAMMAD FAHRI NURHAKIM (MDTU).\n\nTagihan SPP SPP Bulan September telah diterbitkan sebesar *Rp 350.000* dengan jatuh tempo tanggal *16 Sep 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=24262a4694caecb2c9546dae780aff237ba11fdecbfa6bf1fef43736fbf9880f\n\nTerima Kasih.','Terkirim','2026-08-04 16:52:42'),
(86,86,'6282120806947','Bismillah, Yth. Orang Tua dari MUHAMMAD HAFIZH TRIYANT ANARGYA (MDTU).\n\nTagihan SPP SPP Bulan September telah diterbitkan sebesar *Rp 350.000* dengan jatuh tempo tanggal *16 Sep 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=14bec6ee2349c074be9a64a309ee7446001af13294a03f20cd0e725537895a47\n\nTerima Kasih.','Terkirim','2026-08-04 16:52:42'),
(87,87,'6282128512167','Bismillah, Yth. Orang Tua dari MUHAMMAD NURDAFFA AKBAR (MDTU).\n\nTagihan SPP SPP Bulan September telah diterbitkan sebesar *Rp 350.000* dengan jatuh tempo tanggal *16 Sep 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=3ad8a401afd06529ec01dbe051b497ba4aac2ddbb3c56f56457d3c39a375b101\n\nTerima Kasih.','Terkirim','2026-08-04 16:52:42'),
(88,88,'6289647287610','Bismillah, Yth. Orang Tua dari MUHAMMAD RIDWAN SAFI\'I (MDTU).\n\nTagihan SPP SPP Bulan September telah diterbitkan sebesar *Rp 350.000* dengan jatuh tempo tanggal *16 Sep 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=4cc949f0b50712bfb948126c20c3731caffca0a00b3a3f1a3d1361a542d355e5\n\nTerima Kasih.','Terkirim','2026-08-04 16:52:42'),
(89,89,'6282218578119','Bismillah, Yth. Orang Tua dari MUHAMMAD YAZID AL HADROMI (MDTU).\n\nTagihan SPP SPP Bulan September telah diterbitkan sebesar *Rp 350.000* dengan jatuh tempo tanggal *16 Sep 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=381932d358e069af86511515040eb10e11ea1dd06fa46d4eb5ad88ba1d3b09ec\n\nTerima Kasih.','Terkirim','2026-08-04 16:52:42'),
(90,90,'62895705125552','Bismillah, Yth. Orang Tua dari NAJMATU ZHOHIRO PUTRI PRAYITNO (MDTU).\n\nTagihan SPP SPP Bulan September telah diterbitkan sebesar *Rp 350.000* dengan jatuh tempo tanggal *16 Sep 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=8737e61998f1085ce554271c4f198253bd9c007920b8732414da7780fbf45ce6\n\nTerima Kasih.','Terkirim','2026-08-04 16:52:42'),
(91,91,'628995777112','Bismillah, Yth. Orang Tua dari NINO RIZKY ALFATHIR (MDTU).\n\nTagihan SPP SPP Bulan September telah diterbitkan sebesar *Rp 350.000* dengan jatuh tempo tanggal *16 Sep 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=3233b1af72fd464f33eed133003f73132a23229798825caa638d0e512bbbf4f3\n\nTerima Kasih.','Terkirim','2026-08-04 16:52:42'),
(92,92,'6282220225505','Bismillah, Yth. Orang Tua dari RATU BILQIS AZZAHRA (MDTU).\n\nTagihan SPP SPP Bulan September telah diterbitkan sebesar *Rp 350.000* dengan jatuh tempo tanggal *16 Sep 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=8117bbe2cff59642a7c0de632d17a36309a9e44587930ba6f39b7684242d0973\n\nTerima Kasih.','Terkirim','2026-08-04 16:52:42'),
(93,93,'6287777947585','Bismillah, Yth. Orang Tua dari REYHAN TRIYADI (MDTU).\n\nTagihan SPP SPP Bulan September telah diterbitkan sebesar *Rp 350.000* dengan jatuh tempo tanggal *16 Sep 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=6612b33b37b69aeb17e4e37e7e23203fe9a7a5bd9814a352f37f2307d02841d0\n\nTerima Kasih.','Terkirim','2026-08-04 16:52:42'),
(94,94,'6285624471699','Bismillah, Yth. Orang Tua dari SABIYA ALFATHUNNISSA (MDTU).\n\nTagihan SPP SPP Bulan September telah diterbitkan sebesar *Rp 350.000* dengan jatuh tempo tanggal *16 Sep 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=40b7a6818647375b754686e3a369634f28ae1286b31b723de57eeccacf97a6f2\n\nTerima Kasih.','Terkirim','2026-08-04 16:52:42'),
(95,95,'6285838254944','Bismillah, Yth. Orang Tua dari SYIFA DWI TIFANI (MDTU).\n\nTagihan SPP SPP Bulan September telah diterbitkan sebesar *Rp 350.000* dengan jatuh tempo tanggal *16 Sep 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=069f81c018337768fd1ae4894652df576fcb41ffeef466d26a6e5abd6396c927\n\nTerima Kasih.','Terkirim','2026-08-04 16:52:42'),
(96,96,'6281298022414','Bismillah, Yth. Orang Tua dari SYIFA NURUL HASANAH (MDTU).\n\nTagihan SPP SPP Bulan September telah diterbitkan sebesar *Rp 350.000* dengan jatuh tempo tanggal *16 Sep 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=7d559e41f1ac6400e287bc7ea7d1b523af59eec7a95b70a00471af7b9ecd55ea\n\nTerima Kasih.','Terkirim','2026-08-04 16:52:42'),
(97,97,'6289528603392','Bismillah, Yth. Orang Tua dari TRISTAN PRATAMA RIZQULLAH (MDTU).\n\nTagihan SPP SPP Bulan September telah diterbitkan sebesar *Rp 350.000* dengan jatuh tempo tanggal *16 Sep 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=19541c86101663e152ba50dbef31ae5b55740d10dc6a1b02d6197474a0a50102\n\nTerima Kasih.','Terkirim','2026-08-04 16:52:42'),
(98,98,'6282120855223','Bismillah, Yth. Orang Tua dari WISNU ADI KESUMA (MDTU).\n\nTagihan SPP SPP Bulan September telah diterbitkan sebesar *Rp 350.000* dengan jatuh tempo tanggal *16 Sep 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=a6193e5f3a6660ed6d9b0b0178e8c3c8b67c30f05a29fc95bdb2b81cd271c5ca\n\nTerima Kasih.','Terkirim','2026-08-04 16:52:42'),
(99,99,'6287707060734','Bismillah, Yth. Orang Tua dari ZAKIYYA TALITA SAKHI HABIBI (MDTU).\n\nTagihan SPP SPP Bulan September telah diterbitkan sebesar *Rp 350.000* dengan jatuh tempo tanggal *16 Sep 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=d5b01be2b14efec6bb01cc68167b8197b34963863f90193fc1ddcb91e96d1def\n\nTerima Kasih.','Terkirim','2026-08-04 16:52:42'),
(100,100,'62895385313008','Bismillah, Yth. Orang Tua dari ALMEIRA BENAZIR MANAF (MDTU).\n\nTagihan SPP SPP Bulan September telah diterbitkan sebesar *Rp 350.000* dengan jatuh tempo tanggal *16 Sep 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=53a534e62465d9e7fcf27aaa48be15fffe79c4b64bcb6faded69c935f49c69a4\n\nTerima Kasih.','Terkirim','2026-08-04 16:52:42'),
(101,101,'6283804679509','Bismillah, Yth. Orang Tua dari AXCELLO RAFIANDRA SYAHPUTRA (MDTU).\n\nTagihan SPP SPP Bulan September telah diterbitkan sebesar *Rp 350.000* dengan jatuh tempo tanggal *16 Sep 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=abd05e5ab56de924568df5f27f9223ee8521feac4f442e88a16a8fce4a8556ce\n\nTerima Kasih.','Terkirim','2026-08-04 16:52:42'),
(102,102,'6285352876410','Bismillah, Yth. Orang Tua dari AYSILA HUSNA (MDTU).\n\nTagihan SPP SPP Bulan September telah diterbitkan sebesar *Rp 350.000* dengan jatuh tempo tanggal *16 Sep 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=ccd86f05096e66b6ee66ea80573818228333da4b7a8dce56e448ae1e0d9b5425\n\nTerima Kasih.','Terkirim','2026-08-04 16:52:42'),
(103,103,'6285781125668','Bismillah, Yth. Orang Tua dari CAHAYA INDAH DARA (MDTU).\n\nTagihan SPP SPP Bulan September telah diterbitkan sebesar *Rp 350.000* dengan jatuh tempo tanggal *16 Sep 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=04f7801160c053c74ba4b5520234c6482d5d8cd7d1f41cad030980e7a45dba68\n\nTerima Kasih.','Terkirim','2026-08-04 16:52:42'),
(104,104,'6285224070095','Bismillah, Yth. Orang Tua dari GHOSSANY MIR\'ATUL IZZAH (MDTU).\n\nTagihan SPP SPP Bulan September telah diterbitkan sebesar *Rp 350.000* dengan jatuh tempo tanggal *16 Sep 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=c37bf72193cae6c00ed0ed70d326cd42cd2777bc63bb358bfa4660ba1d4ef481\n\nTerima Kasih.','Terkirim','2026-08-04 16:52:42'),
(105,105,'6287828610177','Bismillah, Yth. Orang Tua dari HAFIZHATUL HASNA PUTRI ADELIA (MDTU).\n\nTagihan SPP SPP Bulan September telah diterbitkan sebesar *Rp 350.000* dengan jatuh tempo tanggal *16 Sep 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=c4481899830da3002c5a7d0c6199b50a01c8c6fe800d1ff1fffe476e54bfb317\n\nTerima Kasih.','Terkirim','2026-08-04 16:52:42'),
(106,106,'6287720710953','Bismillah, Yth. Orang Tua dari HERZA KENZO ALVIANO (MDTU).\n\nTagihan SPP SPP Bulan September telah diterbitkan sebesar *Rp 350.000* dengan jatuh tempo tanggal *16 Sep 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=135710efc51b727d14d07ef0837a085e75b8cf518f47357354c77c2197784570\n\nTerima Kasih.','Terkirim','2026-08-04 16:52:42'),
(107,107,'6282240185774','Bismillah, Yth. Orang Tua dari KEIKO RAFIQ ARSYAD (MDTU).\n\nTagihan SPP SPP Bulan September telah diterbitkan sebesar *Rp 350.000* dengan jatuh tempo tanggal *16 Sep 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=703dfac24e1b7ae60252ce270fbcd60ca068f3ab1cd21d215ff2ce691744c1ca\n\nTerima Kasih.','Terkirim','2026-08-04 16:52:42'),
(108,108,'6285199229214','Bismillah, Yth. Orang Tua dari KIRANA AQILLA (MDTU).\n\nTagihan SPP SPP Bulan September telah diterbitkan sebesar *Rp 350.000* dengan jatuh tempo tanggal *16 Sep 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=a6bf8336fa4c4ec0bf26f646dde59f099e22e70d103ee9587ab5ce29829a7feb\n\nTerima Kasih.','Terkirim','2026-08-04 16:52:42'),
(109,109,'6289529449085','Bismillah, Yth. Orang Tua dari MALIQ AL RASYID (MDTU).\n\nTagihan SPP SPP Bulan September telah diterbitkan sebesar *Rp 350.000* dengan jatuh tempo tanggal *16 Sep 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=38adcf79b313cce9714df7e5773144d64e35cfd003927748dadf9b40d69fbd26\n\nTerima Kasih.','Terkirim','2026-08-04 16:52:42'),
(110,110,'628121459427','Bismillah, Yth. Orang Tua dari MAULIDA ZULFA ELZUHARA (MDTU).\n\nTagihan SPP SPP Bulan September telah diterbitkan sebesar *Rp 350.000* dengan jatuh tempo tanggal *16 Sep 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=fc0dfca8412d66b65b242908dd894987e332cc68282557689a010924ff506d44\n\nTerima Kasih.','Terkirim','2026-08-04 16:52:42'),
(111,111,'6287894263121','Bismillah, Yth. Orang Tua dari MOH. AFFSAN SAPUTRA (MDTU).\n\nTagihan SPP SPP Bulan September telah diterbitkan sebesar *Rp 350.000* dengan jatuh tempo tanggal *16 Sep 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=ff4ae39041c69bd179d5f80d3428009ae1a9f0f5344613da27bdb949a8897892\n\nTerima Kasih.','Terkirim','2026-08-04 16:52:42'),
(112,112,'6289661061526','Bismillah, Yth. Orang Tua dari MUSYRIF MUHAMMAD RIZQIANSYAH (MDTU).\n\nTagihan SPP SPP Bulan September telah diterbitkan sebesar *Rp 350.000* dengan jatuh tempo tanggal *16 Sep 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=ecdc36492b095298b512f70c73c6e3cde0a73aca0a20fe7412121b1e1b30c013\n\nTerima Kasih.','Terkirim','2026-08-04 16:52:42'),
(113,113,'6282320257930','Bismillah, Yth. Orang Tua dari NARESWARA PUTRA BAHARI (MDTU).\n\nTagihan SPP SPP Bulan September telah diterbitkan sebesar *Rp 350.000* dengan jatuh tempo tanggal *16 Sep 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=63980783b9ec90f9f17761bd72269321f0adc60326ad61fa3f6e1d4cfc88c5c3\n\nTerima Kasih.','Terkirim','2026-08-04 16:52:42'),
(114,114,'6281564948899','Bismillah, Yth. Orang Tua dari NASHA KIREI AZZAHRA (MDTU).\n\nTagihan SPP SPP Bulan September telah diterbitkan sebesar *Rp 350.000* dengan jatuh tempo tanggal *16 Sep 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=21fbb07764dda123b0065ead655caf9d17c50b61b627394cad185955a3ec2d0a\n\nTerima Kasih.','Terkirim','2026-08-04 16:52:42'),
(115,115,'628960894005','Bismillah, Yth. Orang Tua dari SEFTI MARWAH AZAHRA (MDTU).\n\nTagihan SPP SPP Bulan September telah diterbitkan sebesar *Rp 350.000* dengan jatuh tempo tanggal *16 Sep 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=4b20f85e9f3270f9ada0b64b178abbca0f50746ecdf6e4fec2b4e297104712c3\n\nTerima Kasih.','Terkirim','2026-08-04 16:52:42'),
(116,116,'6289513018292','Bismillah, Yth. Orang Tua dari YASBIH QUUINSYAH SETIAWAN (MDTU).\n\nTagihan SPP SPP Bulan September telah diterbitkan sebesar *Rp 350.000* dengan jatuh tempo tanggal *16 Sep 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=4d2caf82e75df53fb23bf1b17e6bca6cbce4a8ac0271a9accb99722ea384ef73\n\nTerima Kasih.','Terkirim','2026-08-04 16:52:42'),
(117,117,'6289675812465','Bismillah, Yth. Orang Tua dari ZAFIN AZHARI (MDTU).\n\nTagihan SPP SPP Bulan September telah diterbitkan sebesar *Rp 350.000* dengan jatuh tempo tanggal *16 Sep 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=739d9a545ad142796dd3bf93130e295ca2e9c9f841478605b33ab22b32186cf8\n\nTerima Kasih.','Terkirim','2026-08-04 16:52:42'),
(118,118,'6282316367755','Bismillah, Yth. Orang Tua dari MUHAMMAD AZZAM EL-SYAUQI (MDTU).\n\nTagihan SPP SPP Bulan September telah diterbitkan sebesar *Rp 350.000* dengan jatuh tempo tanggal *16 Sep 2026*.\n\nLink pembayaran: https://rqt.porcalabs.my.id/spp_portal.php?token=dd422bf1e74460a59e86900b2cef4af531cc6861687f83d5b1e6d0e736ff7ce4\n\nTerima Kasih.','Terkirim','2026-08-04 16:52:42');
/*!40000 ALTER TABLE `spp_log_notifikasi` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `spp_pembayaran`
--

DROP TABLE IF EXISTS `spp_pembayaran`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `spp_pembayaran` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nomor_transaksi` varchar(50) NOT NULL,
  `tagihan_id` int(11) NOT NULL,
  `nis` varchar(20) NOT NULL,
  `metode` enum('Tunai','Transfer','QRIS') NOT NULL,
  `nominal` decimal(12,2) NOT NULL,
  `bukti_transfer` varchar(255) DEFAULT NULL,
  `status_verifikasi` enum('Pending','Disetujui','Ditolak') DEFAULT 'Pending',
  `catatan` text DEFAULT NULL,
  `petugas_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `nomor_transaksi` (`nomor_transaksi`),
  KEY `tagihan_id` (`tagihan_id`),
  KEY `nis` (`nis`),
  KEY `status_verifikasi` (`status_verifikasi`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `spp_pembayaran`
--

LOCK TABLES `spp_pembayaran` WRITE;
/*!40000 ALTER TABLE `spp_pembayaran` DISABLE KEYS */;
INSERT INTO `spp_pembayaran` VALUES
(1,'SPP-20260804-4449',59,'22425.1.012','Tunai',100000.00,NULL,'Disetujui','',1,'2026-08-04 16:44:38'),
(2,'SPP-20260804-8571',57,'22627.1.017','Tunai',50000.00,NULL,'Disetujui','',1,'2026-08-04 16:54:24');
/*!40000 ALTER TABLE `spp_pembayaran` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `spp_pengaturan`
--

DROP TABLE IF EXISTS `spp_pengaturan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `spp_pengaturan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_bank` varchar(50) DEFAULT 'Bank BCA',
  `no_rekening` varchar(50) DEFAULT '1234567890',
  `atas_nama` varchar(100) DEFAULT 'Rumah Quran Temi',
  `qris_image` varchar(255) DEFAULT 'qris_default.png',
  `wa_template_tagihan` text DEFAULT NULL,
  `wa_template_lunas` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `spp_pengaturan`
--

LOCK TABLES `spp_pengaturan` WRITE;
/*!40000 ALTER TABLE `spp_pengaturan` DISABLE KEYS */;
INSERT INTO `spp_pengaturan` VALUES
(1,'Bank BCA','1234567890','Rumah Qur\'an Temi','qris_default.png','Bismillah, Yth. Orang Tua dari {nama_siswa} ({kelas}).\n\nTagihan SPP {nama_tagihan} telah diterbitkan sebesar *Rp {nominal}* dengan jatuh tempo tanggal *{jatuh_tempo}*.\n\nLink pembayaran: {link_portal}\n\nTerima Kasih.','Alhamdulillah, Pembayaran {nama_tagihan} sebesar *Rp {nominal}* untuk {nama_siswa} telah DITERIMA dan VERIFIKASI (LUNAS).\n\nKwitansi digital: {link_kwitansi}\n\nTerima kasih atas partisipasinya.');
/*!40000 ALTER TABLE `spp_pengaturan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `spp_tagihan`
--

DROP TABLE IF EXISTS `spp_tagihan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `spp_tagihan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nis` varchar(20) NOT NULL,
  `jenis_tagihan_id` int(11) NOT NULL,
  `nominal` decimal(12,2) NOT NULL,
  `dibayar` decimal(12,2) DEFAULT 0.00,
  `sisa` decimal(12,2) NOT NULL,
  `status` enum('Belum Bayar','Sebagian','Lunas','Terlambat','Dibatalkan') DEFAULT 'Belum Bayar',
  `token` varchar(64) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `token` (`token`),
  KEY `nis` (`nis`),
  KEY `jenis_tagihan_id` (`jenis_tagihan_id`),
  KEY `status` (`status`),
  KEY `token_2` (`token`)
) ENGINE=InnoDB AUTO_INCREMENT=119 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `spp_tagihan`
--

LOCK TABLES `spp_tagihan` WRITE;
/*!40000 ALTER TABLE `spp_tagihan` DISABLE KEYS */;
INSERT INTO `spp_tagihan` VALUES
(1,'22425.1.001',2,100000.00,0.00,100000.00,'Belum Bayar','57463c81c1e420c920f08a774e849ad7bed9f6a70cc9e8fcfa07ee324ba77f18','2026-08-04 16:40:06'),
(2,'22425.1.004',2,100000.00,0.00,100000.00,'Belum Bayar','fd14d89a55d855a29195bd2227426718c07a1e91087ec9be0ee5491f5751f8ba','2026-08-04 16:40:06'),
(3,'22526.1.002',2,100000.00,0.00,100000.00,'Belum Bayar','36c2713c8b63c7d6905cada87f6c1395dc93279fcf0bdb888d096f189ec6846d','2026-08-04 16:40:06'),
(4,'22324.1.004',2,100000.00,0.00,100000.00,'Belum Bayar','e6b8f3a9182c0b1e69549bfb58b86708d9e6b882aaec41c6cc08c45c768afd89','2026-08-04 16:40:06'),
(5,'22526.1.003',2,100000.00,0.00,100000.00,'Belum Bayar','393c83a010b98673944b3befcce1fe51c9967fb394dc620deb647d973de8d46f','2026-08-04 16:40:06'),
(6,'22324.1.008',2,100000.00,0.00,100000.00,'Belum Bayar','c128e3a620b3777af6cdb5abd96373581ca26582bc67369b4b605956fb91555b','2026-08-04 16:40:06'),
(7,'22425.1.023',2,100000.00,0.00,100000.00,'Belum Bayar','4c88313dc14d331ae92a73f37d3fa7ee8ec1bc37bfa2263d84eba549a5cf0778','2026-08-04 16:40:06'),
(8,'22526.1.005',2,100000.00,0.00,100000.00,'Belum Bayar','91db6214cfbebc78c2663581d76631d5acde8f4da8f00e957372960100fd6e79','2026-08-04 16:40:06'),
(9,'22425.1.006',2,100000.00,0.00,100000.00,'Belum Bayar','bdb4025999c82fe0228b0b96e9f381baf31164ea1d1329e7df30e39ed4d84212','2026-08-04 16:40:06'),
(10,'22425.1.007',2,100000.00,0.00,100000.00,'Belum Bayar','a5b102948edbbeeb5e92ef74a002d7bf16edadb46b60eb4ad8f5d09ba4335540','2026-08-04 16:40:06'),
(11,'22526.1.027',2,100000.00,0.00,100000.00,'Belum Bayar','aebb96c7eeedae91dac89f3b68074622c6c4d05a46293065166df70a1538eb8b','2026-08-04 16:40:06'),
(12,'22526.1.007',2,100000.00,0.00,100000.00,'Belum Bayar','cbc3634432fe70f567d97db6b608b1626cda470c1f97f89639b75855ce970ed8','2026-08-04 16:40:06'),
(13,'22526.1.008',2,100000.00,0.00,100000.00,'Belum Bayar','a1de65f497ba9b572071ce6d4e60aaed646f0479c99d675435c6ba6f41516846','2026-08-04 16:40:06'),
(14,'22425.1.008',2,100000.00,0.00,100000.00,'Belum Bayar','6c4bacb6811f9da3c645c457f34625510004f7ae7cbbd8d8ec973de0d055f82d','2026-08-04 16:40:06'),
(15,'22526.1.012',2,100000.00,0.00,100000.00,'Belum Bayar','13c54a60056db0b62c84c1cdb8913d2ba3dd51a335292d82d428fd5759c520e2','2026-08-04 16:40:06'),
(16,'22526.1.013',2,100000.00,0.00,100000.00,'Belum Bayar','0b51dc13ffbd6f4b377eb774e7d4b3843e92c65998ddaaf5b82e424e784075e7','2026-08-04 16:40:06'),
(17,'22425.1.009',2,100000.00,0.00,100000.00,'Belum Bayar','2031fcb74def6eaa5cfb62df5edc621fdc96a020d0bd6b8731249dcfd979aa6d','2026-08-04 16:40:06'),
(18,'22425.1.010',2,100000.00,0.00,100000.00,'Belum Bayar','92e170290856e7fdae2fd6f7187a9eb7b4754059b10c63cd4607c7ce39a5019f','2026-08-04 16:40:06'),
(19,'22324.1.018',2,100000.00,0.00,100000.00,'Belum Bayar','e1f44ac23fe65fe521cf7a25549da8c911c395fa53c6f32814da4d81c8fb6528','2026-08-04 16:40:06'),
(20,'22324.1.011',2,100000.00,0.00,100000.00,'Belum Bayar','94ec9c76f2dfa5f7174f3feb81d88d940a9bf5946862c2c80d91cbfe364d353a','2026-08-04 16:40:06'),
(21,'22425.1.011',2,100000.00,0.00,100000.00,'Belum Bayar','3a2193d3306ff4cd6fa1d92887ffe4f9455ac7dd8849e9d1c157e8ae3ce1974c','2026-08-04 16:40:06'),
(22,'22526.1.015',2,100000.00,0.00,100000.00,'Belum Bayar','5f7eddafc57a79f3e658c936e42ee45485d3608dc2933f5db40d903a7ff9cdac','2026-08-04 16:40:06'),
(23,'22526.1.016',2,100000.00,0.00,100000.00,'Belum Bayar','18466e02a1081c84380d013780322f18a4507bc5e781d58797c6d01081632834','2026-08-04 16:40:06'),
(24,'22526.1.018',2,100000.00,0.00,100000.00,'Belum Bayar','1d1ae9e6000a248d495fc314b1f484fa70e9556b46be78c5c8a359e1511d072a','2026-08-04 16:40:06'),
(25,'22526.1.020',2,100000.00,0.00,100000.00,'Belum Bayar','f861797e6cf03a89841a967e709c83fd2ee0516a65ac69ad849cbe12b2e287c7','2026-08-04 16:40:06'),
(26,'22526.1.021',2,100000.00,0.00,100000.00,'Belum Bayar','f9a26740debe548261c09dac9242579e1610e14b4812cb6feff94f8c38389517','2026-08-04 16:40:06'),
(27,'22324.1.014',2,100000.00,0.00,100000.00,'Belum Bayar','9c711dcde52199ec227d5cd1f7a51d58eaed1fdd9de978cf0a0a52b86b53c4c7','2026-08-04 16:40:06'),
(28,'22425.1.013',2,100000.00,0.00,100000.00,'Belum Bayar','f93aef6c85c3e9423b103bce419dfc8958141703b5ee754ccdfa43440ce177d1','2026-08-04 16:40:06'),
(29,'22425.1.014',2,100000.00,0.00,100000.00,'Belum Bayar','b765bc1304e3955aa1298d21c9955ecb0b35bcc23ff8f88a3fff82c9d9e72e7b','2026-08-04 16:40:06'),
(30,'22425.1.015',2,100000.00,0.00,100000.00,'Belum Bayar','fb64f8d0bba054a5455e0c13e2e83896af673ec65433a6abc01c0e105b0657e5','2026-08-04 16:40:06'),
(31,'22324.1.013',2,100000.00,0.00,100000.00,'Belum Bayar','7e2192527038c8768331116cc645d559c9b2cfbab90fe4166012c15da2b53fb9','2026-08-04 16:40:06'),
(32,'22324.1.016',2,100000.00,0.00,100000.00,'Belum Bayar','cec87fd4532e66971c2d2db05749c56d943c72082a9470e34f04c6be343ca53c','2026-08-04 16:40:06'),
(33,'22526.1.023',2,100000.00,0.00,100000.00,'Belum Bayar','b73b6cd9c403bb0ddd9f50c1de2d5cc61058b85c1f09503db103974b988e48b6','2026-08-04 16:40:06'),
(34,'22526.1.024',2,100000.00,0.00,100000.00,'Belum Bayar','f52165f77bc9b2f88fff5b7163c974d70e31380ebc11c603d45fe2a12b6dc108','2026-08-04 16:40:06'),
(35,'22425.1.018',2,100000.00,0.00,100000.00,'Belum Bayar','7f712fcba45a0f1cb7217007f8764f3051132d422b74b3040fcf86368296dc63','2026-08-04 16:40:06'),
(36,'22324.1.010',2,100000.00,0.00,100000.00,'Belum Bayar','8b211fc7653912ffceaf7ccfea2dad79621a5c7eaca8934e0afa769f86e9fdcd','2026-08-04 16:40:06'),
(37,'22526.1.026',2,100000.00,0.00,100000.00,'Belum Bayar','7de7e839a45d0eb52672e15095bea4ef7c1b006e7e7cb39f3504932c2616d457','2026-08-04 16:40:06'),
(38,'22425.1.020',2,100000.00,0.00,100000.00,'Belum Bayar','01b9c497b9376e91162c8af4b5ecb91c3ee59b1d772b4256299fdb60f9297093','2026-08-04 16:40:06'),
(39,'22425.1.022',2,100000.00,0.00,100000.00,'Belum Bayar','1d577a02ea90c8a4074cfda01bb0531d6ad847448289ccbf6b5bc811b2752183','2026-08-04 16:40:06'),
(40,'22324.1.017',2,100000.00,0.00,100000.00,'Belum Bayar','98288e5625d1cfbb61bb67890d0fd6116e1467eeb48b7a69f3dd4d1ec37043fd','2026-08-04 16:40:06'),
(41,'22627.1.001',2,100000.00,0.00,100000.00,'Belum Bayar','7f3743a0f3e3df096b6bde3152a9c562400e52c6f103e99ae287e079864a4128','2026-08-04 16:40:06'),
(42,'22627.1.002',2,100000.00,0.00,100000.00,'Belum Bayar','cdb6a8c972b82aa22050e63df00a568db4220216da50e3be3081d1f26a1b793f','2026-08-04 16:40:06'),
(43,'22627.1.003',2,100000.00,0.00,100000.00,'Belum Bayar','ad0a82fa8c0b134cfa957d738050d3c223b7bcafc570063c521ccfe1cde10adb','2026-08-04 16:40:06'),
(44,'22627.1.004',2,100000.00,0.00,100000.00,'Belum Bayar','464b4ced2b83fe910797ee34d93c2b119cbf6d1accaca2e44a7605d3ec3b811f','2026-08-04 16:40:06'),
(45,'22627.1.005',2,100000.00,0.00,100000.00,'Belum Bayar','f121dc7df2bf91647d0106cacc57b6d21ae05731e1c93ccd6d586b1b04504664','2026-08-04 16:40:06'),
(46,'22627.1.006',2,100000.00,0.00,100000.00,'Belum Bayar','44d874258148ad1516bbe4d6ed38160e3ae44ac8ceaa53968d70c4da48bb72b7','2026-08-04 16:40:06'),
(47,'22627.1.007',2,100000.00,0.00,100000.00,'Belum Bayar','a839de47d486cafb38a2c267784febbbb297e43204b18caf5f40698f58aa3ba2','2026-08-04 16:40:06'),
(48,'22627.1.008',2,100000.00,0.00,100000.00,'Belum Bayar','e434915fc6d7d75925a1048c3f98523530252c17102c40548efd446b9001f37c','2026-08-04 16:40:06'),
(49,'22627.1.009',2,100000.00,0.00,100000.00,'Belum Bayar','760fdf7bb310c983383995c823f74b42211f85969e82764edc2ee3f890ccd691','2026-08-04 16:40:06'),
(50,'22627.1.010',2,100000.00,0.00,100000.00,'Belum Bayar','6cc0a74e3f82c9a1148a4805e1a6970c4b32bb6db3eb99fb57e200e95a93c7ee','2026-08-04 16:40:06'),
(51,'22627.1.011',2,100000.00,0.00,100000.00,'Belum Bayar','0983521cd3f7e20b4aa00f766062f3fbce8cd7b9a134017a65d29834873ac34e','2026-08-04 16:40:06'),
(52,'22627.1.012',2,100000.00,0.00,100000.00,'Belum Bayar','f7d02e02e9bab0d68cb0de0645940d9fd59062ace1a4332d2994d4264b7077c5','2026-08-04 16:40:06'),
(53,'22627.1.013',2,100000.00,0.00,100000.00,'Belum Bayar','5d18401952b26f3d6c5d4566ae72d33659597182e6eb9cf3ae2c59ddd3039a81','2026-08-04 16:40:06'),
(54,'22627.1.014',2,100000.00,0.00,100000.00,'Belum Bayar','375848658696e6a918d903604ccd668df6a9502ab86bbbdb1cb065d65a7669a2','2026-08-04 16:40:06'),
(55,'22627.1.015',2,100000.00,0.00,100000.00,'Belum Bayar','e1773776cb67b2549ed116ee2e53cfd12de7877bdbd5f5d3384af2c11f83d5ed','2026-08-04 16:40:06'),
(56,'22627.1.016',2,100000.00,0.00,100000.00,'Belum Bayar','6c92b87e4deac753912f9089df94bc9422fb2029d9941b976a67d6065e9c9a16','2026-08-04 16:40:06'),
(57,'22627.1.017',2,100000.00,50000.00,50000.00,'Sebagian','42f3e368403033549be665845e9f3a1f212a19a2753b2731c8b00c00c042de1e','2026-08-04 16:40:06'),
(58,'22627.1.018',2,100000.00,0.00,100000.00,'Belum Bayar','069d06b57dc4fd288f2110ad36d27a90f28b174560ca06f3ab7947f35668bde1','2026-08-04 16:40:06'),
(59,'22425.1.012',2,100000.00,100000.00,0.00,'Lunas','06006e2f7df750a1acb00480ab2cf77ef37f4dddb20f2cf7372c2b4312c86314','2026-08-04 16:40:06'),
(60,'22425.1.001',3,350000.00,0.00,350000.00,'Belum Bayar','a26e62b8838fe516c734e75ee0aff39abc13add3a15b9bf0503bf72039b8cce8','2026-08-04 16:52:42'),
(61,'22425.1.004',3,350000.00,0.00,350000.00,'Belum Bayar','5b915b2100a63c6b01ea9deac4ab2f0b2d5fc51c2330950c6ea413b61047d8f6','2026-08-04 16:52:42'),
(62,'22526.1.002',3,350000.00,0.00,350000.00,'Belum Bayar','b007b0cac300a2221cdb4ff615654ca67eb5c21e8daaa187fb189d25e0a9e38a','2026-08-04 16:52:42'),
(63,'22324.1.004',3,350000.00,0.00,350000.00,'Belum Bayar','877eee22e430b032108abaf559e6a3d841206748dfaf8953cb79746ddd41e7e2','2026-08-04 16:52:42'),
(64,'22526.1.003',3,350000.00,0.00,350000.00,'Belum Bayar','b3b3fa0cac10737243a3b67615d7231333793e3cec181d014a04b7676b99d7cb','2026-08-04 16:52:42'),
(65,'22324.1.008',3,350000.00,0.00,350000.00,'Belum Bayar','ebc92df940c5ca5d4e830b0e91aae760f6e0b104172266ecb0dcb8c9f65bbbc3','2026-08-04 16:52:42'),
(66,'22425.1.023',3,350000.00,0.00,350000.00,'Belum Bayar','e98e0c01b904e086a03e28d9bc936d460bf4ba79ae58378bae2abd40e607cc9d','2026-08-04 16:52:42'),
(67,'22526.1.005',3,350000.00,0.00,350000.00,'Belum Bayar','2dd59add51b50aa9396df27f3eb15a8fe3b515ccc82343e935656f94405562e0','2026-08-04 16:52:42'),
(68,'22425.1.006',3,350000.00,0.00,350000.00,'Belum Bayar','7dd79ecf5da156486b1e3f3ed8c979d1f1a5948772221321389022f8c93fe5d4','2026-08-04 16:52:42'),
(69,'22425.1.007',3,350000.00,0.00,350000.00,'Belum Bayar','64583d8dd29c07997ba8840e17304aca5159547c5fcd5192cd028db9d5027c8c','2026-08-04 16:52:42'),
(70,'22526.1.027',3,350000.00,0.00,350000.00,'Belum Bayar','ed5b1d76fe24c515b76f6f7a108aeb96fb41db03fb98449f899dced31be0d24a','2026-08-04 16:52:42'),
(71,'22526.1.007',3,350000.00,0.00,350000.00,'Belum Bayar','ddf12a0278879f588875df92f1b30af412c48bf455cc85f998574e7d2825c50a','2026-08-04 16:52:42'),
(72,'22526.1.008',3,350000.00,0.00,350000.00,'Belum Bayar','4b0ca12839c57012309c7922a5c2c565888e43ef7bac79c320bdf3d436d85008','2026-08-04 16:52:42'),
(73,'22425.1.008',3,350000.00,0.00,350000.00,'Belum Bayar','4eb77f582f12f851716d1a8dc619f08728dbb35e101a0f4a3e3dcf3f13269f39','2026-08-04 16:52:42'),
(74,'22526.1.012',3,350000.00,0.00,350000.00,'Belum Bayar','add818b39e29e8138b3029412201865693bb4818d2aa956a7ccc22ad61359b0d','2026-08-04 16:52:42'),
(75,'22526.1.013',3,350000.00,0.00,350000.00,'Belum Bayar','5fe990422f5f30b4838b6e3d5bf2feaeb21141245b00f10ea9c9f98e7d55679c','2026-08-04 16:52:42'),
(76,'22425.1.009',3,350000.00,0.00,350000.00,'Belum Bayar','30502f72772842971ac600819b397e3ceec90c29824947a39d8ae7faa4eef3d8','2026-08-04 16:52:42'),
(77,'22425.1.010',3,350000.00,0.00,350000.00,'Belum Bayar','5213326cebfd4379d655de8f1257fa82c455e56adda0fab25083979ff34705ab','2026-08-04 16:52:42'),
(78,'22324.1.018',3,350000.00,0.00,350000.00,'Belum Bayar','4083d87a0712cbb2cdcfbce4a812d1a24f86ea76a910c06e83c0247d77320d84','2026-08-04 16:52:42'),
(79,'22324.1.011',3,350000.00,0.00,350000.00,'Belum Bayar','271231493e7c66088eaadaeb360940995a06e3f744e8aae874a68ed534f94ba4','2026-08-04 16:52:42'),
(80,'22425.1.011',3,350000.00,0.00,350000.00,'Belum Bayar','1943f05737e93d8d1a07c6bf7999adf87bb06449d0e064f2e866a5a4e7cc55f4','2026-08-04 16:52:42'),
(81,'22526.1.015',3,350000.00,0.00,350000.00,'Belum Bayar','521b6b237e0637d756e3be971f452c75d858b86edd1f458b38d09eaf64a1231b','2026-08-04 16:52:42'),
(82,'22526.1.016',3,350000.00,0.00,350000.00,'Belum Bayar','09956e1a0eec54ca9c11f98e1d6038b4eda9a307416708635afb8d32348d5903','2026-08-04 16:52:42'),
(83,'22526.1.018',3,350000.00,0.00,350000.00,'Belum Bayar','74d8e4f736fb47981d9830dcf2e2445ac50b98f68cb1fc0d9caf581459befdb3','2026-08-04 16:52:42'),
(84,'22526.1.020',3,350000.00,0.00,350000.00,'Belum Bayar','01f061838160ff8d048081c1dbd6e9a29dedf19846af983ef6ed79bd18ce2f54','2026-08-04 16:52:42'),
(85,'22526.1.021',3,350000.00,0.00,350000.00,'Belum Bayar','24262a4694caecb2c9546dae780aff237ba11fdecbfa6bf1fef43736fbf9880f','2026-08-04 16:52:42'),
(86,'22324.1.014',3,350000.00,0.00,350000.00,'Belum Bayar','14bec6ee2349c074be9a64a309ee7446001af13294a03f20cd0e725537895a47','2026-08-04 16:52:42'),
(87,'22425.1.013',3,350000.00,0.00,350000.00,'Belum Bayar','3ad8a401afd06529ec01dbe051b497ba4aac2ddbb3c56f56457d3c39a375b101','2026-08-04 16:52:42'),
(88,'22425.1.014',3,350000.00,0.00,350000.00,'Belum Bayar','4cc949f0b50712bfb948126c20c3731caffca0a00b3a3f1a3d1361a542d355e5','2026-08-04 16:52:42'),
(89,'22425.1.015',3,350000.00,0.00,350000.00,'Belum Bayar','381932d358e069af86511515040eb10e11ea1dd06fa46d4eb5ad88ba1d3b09ec','2026-08-04 16:52:42'),
(90,'22324.1.013',3,350000.00,0.00,350000.00,'Belum Bayar','8737e61998f1085ce554271c4f198253bd9c007920b8732414da7780fbf45ce6','2026-08-04 16:52:42'),
(91,'22324.1.016',3,350000.00,0.00,350000.00,'Belum Bayar','3233b1af72fd464f33eed133003f73132a23229798825caa638d0e512bbbf4f3','2026-08-04 16:52:42'),
(92,'22526.1.023',3,350000.00,0.00,350000.00,'Belum Bayar','8117bbe2cff59642a7c0de632d17a36309a9e44587930ba6f39b7684242d0973','2026-08-04 16:52:42'),
(93,'22526.1.024',3,350000.00,0.00,350000.00,'Belum Bayar','6612b33b37b69aeb17e4e37e7e23203fe9a7a5bd9814a352f37f2307d02841d0','2026-08-04 16:52:42'),
(94,'22425.1.018',3,350000.00,0.00,350000.00,'Belum Bayar','40b7a6818647375b754686e3a369634f28ae1286b31b723de57eeccacf97a6f2','2026-08-04 16:52:42'),
(95,'22324.1.010',3,350000.00,0.00,350000.00,'Belum Bayar','069f81c018337768fd1ae4894652df576fcb41ffeef466d26a6e5abd6396c927','2026-08-04 16:52:42'),
(96,'22526.1.026',3,350000.00,0.00,350000.00,'Belum Bayar','7d559e41f1ac6400e287bc7ea7d1b523af59eec7a95b70a00471af7b9ecd55ea','2026-08-04 16:52:42'),
(97,'22425.1.020',3,350000.00,0.00,350000.00,'Belum Bayar','19541c86101663e152ba50dbef31ae5b55740d10dc6a1b02d6197474a0a50102','2026-08-04 16:52:42'),
(98,'22425.1.022',3,350000.00,0.00,350000.00,'Belum Bayar','a6193e5f3a6660ed6d9b0b0178e8c3c8b67c30f05a29fc95bdb2b81cd271c5ca','2026-08-04 16:52:42'),
(99,'22324.1.017',3,350000.00,0.00,350000.00,'Belum Bayar','d5b01be2b14efec6bb01cc68167b8197b34963863f90193fc1ddcb91e96d1def','2026-08-04 16:52:42'),
(100,'22627.1.001',3,350000.00,0.00,350000.00,'Belum Bayar','53a534e62465d9e7fcf27aaa48be15fffe79c4b64bcb6faded69c935f49c69a4','2026-08-04 16:52:42'),
(101,'22627.1.002',3,350000.00,0.00,350000.00,'Belum Bayar','abd05e5ab56de924568df5f27f9223ee8521feac4f442e88a16a8fce4a8556ce','2026-08-04 16:52:42'),
(102,'22627.1.003',3,350000.00,0.00,350000.00,'Belum Bayar','ccd86f05096e66b6ee66ea80573818228333da4b7a8dce56e448ae1e0d9b5425','2026-08-04 16:52:42'),
(103,'22627.1.004',3,350000.00,0.00,350000.00,'Belum Bayar','04f7801160c053c74ba4b5520234c6482d5d8cd7d1f41cad030980e7a45dba68','2026-08-04 16:52:42'),
(104,'22627.1.005',3,350000.00,0.00,350000.00,'Belum Bayar','c37bf72193cae6c00ed0ed70d326cd42cd2777bc63bb358bfa4660ba1d4ef481','2026-08-04 16:52:42'),
(105,'22627.1.006',3,350000.00,0.00,350000.00,'Belum Bayar','c4481899830da3002c5a7d0c6199b50a01c8c6fe800d1ff1fffe476e54bfb317','2026-08-04 16:52:42'),
(106,'22627.1.007',3,350000.00,0.00,350000.00,'Belum Bayar','135710efc51b727d14d07ef0837a085e75b8cf518f47357354c77c2197784570','2026-08-04 16:52:42'),
(107,'22627.1.008',3,350000.00,0.00,350000.00,'Belum Bayar','703dfac24e1b7ae60252ce270fbcd60ca068f3ab1cd21d215ff2ce691744c1ca','2026-08-04 16:52:42'),
(108,'22627.1.009',3,350000.00,0.00,350000.00,'Belum Bayar','a6bf8336fa4c4ec0bf26f646dde59f099e22e70d103ee9587ab5ce29829a7feb','2026-08-04 16:52:42'),
(109,'22627.1.010',3,350000.00,0.00,350000.00,'Belum Bayar','38adcf79b313cce9714df7e5773144d64e35cfd003927748dadf9b40d69fbd26','2026-08-04 16:52:42'),
(110,'22627.1.011',3,350000.00,0.00,350000.00,'Belum Bayar','fc0dfca8412d66b65b242908dd894987e332cc68282557689a010924ff506d44','2026-08-04 16:52:42'),
(111,'22627.1.012',3,350000.00,0.00,350000.00,'Belum Bayar','ff4ae39041c69bd179d5f80d3428009ae1a9f0f5344613da27bdb949a8897892','2026-08-04 16:52:42'),
(112,'22627.1.013',3,350000.00,0.00,350000.00,'Belum Bayar','ecdc36492b095298b512f70c73c6e3cde0a73aca0a20fe7412121b1e1b30c013','2026-08-04 16:52:42'),
(113,'22627.1.014',3,350000.00,0.00,350000.00,'Belum Bayar','63980783b9ec90f9f17761bd72269321f0adc60326ad61fa3f6e1d4cfc88c5c3','2026-08-04 16:52:42'),
(114,'22627.1.015',3,350000.00,0.00,350000.00,'Belum Bayar','21fbb07764dda123b0065ead655caf9d17c50b61b627394cad185955a3ec2d0a','2026-08-04 16:52:42'),
(115,'22627.1.016',3,350000.00,0.00,350000.00,'Belum Bayar','4b20f85e9f3270f9ada0b64b178abbca0f50746ecdf6e4fec2b4e297104712c3','2026-08-04 16:52:42'),
(116,'22627.1.017',3,350000.00,0.00,350000.00,'Belum Bayar','4d2caf82e75df53fb23bf1b17e6bca6cbce4a8ac0271a9accb99722ea384ef73','2026-08-04 16:52:42'),
(117,'22627.1.018',3,350000.00,0.00,350000.00,'Belum Bayar','739d9a545ad142796dd3bf93130e295ca2e9c9f841478605b33ab22b32186cf8','2026-08-04 16:52:42'),
(118,'22425.1.012',3,350000.00,0.00,350000.00,'Belum Bayar','dd422bf1e74460a59e86900b2cef4af531cc6861687f83d5b1e6d0e736ff7ce4','2026-08-04 16:52:42');
/*!40000 ALTER TABLE `spp_tagihan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','walikelas','piket','kantin','bendahara') NOT NULL DEFAULT 'walikelas',
  `kelas_diampu` varchar(20) DEFAULT NULL,
  `nama_lengkap` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES
(1,'admin','$2y$10$cD3iHhZ9MuNRHlHd0VW5UOeWh.GFcFn1JjbDcFwZwA1ILoN0A22Ra','admin',NULL,'Solahudin Al Ayubih, S.Kom, Gr.'),
(7,'nurhasanah','$2y$10$41SUwQTWJiIxXXmJ./z1fe4vSTZdJbnyd5NukNz7FcpM40kXTHKcS','kantin',NULL,'Nurhasanah'),
(8,'nana_rusmana','$2y$10$.eGzI.FfQsU5vAsiaAt6FOgEBDUuvGhgTqb0/sQi8y0ZVWI8fSXWq','bendahara',NULL,'Nana Rusmana'),
(9,'nabila_azzahra','$2y$10$yFllZutE5mxozGXMLX.v9uyVG6Oa8E4.XhP2TcibC3yJ7ZMcofbju','bendahara',NULL,'Nabila Azzahra'),
(10,'yati_supriati','$2y$10$w6.l1Q0xO4M.Gq5UfX7v9eO.p31hW8JqQ5m5M9X9c7M5z7w4/k65a','admin',NULL,'Yati Supriati'),
(11,'ditya_dwi','$2y$10$w6.l1Q0xO4M.Gq5UfX7v9eO.p31hW8JqQ5m5M9X9c7M5z7w4/k65a','admin',NULL,'Ditya Dwi Edy S.A'),
(12,'sri_anjani','$2y$10$w6.l1Q0xO4M.Gq5UfX7v9eO.p31hW8JqQ5m5M9X9c7M5z7w4/k65a','walikelas','TKQ','Sri Anjani'),
(13,'eli_rizkiana','$2y$10$pkXKnHQkCZjp21t9ltXfMefvOojTVaVU7n3oQ6qRUGQ8mTFeIhuau','walikelas','TKQ','Eli Rizkiana'),
(14,'fauziyah','$2y$10$4xVCZK6dTeOWwjLoDo2O0uV1YklTMi9zr6DBFs62SJ9af.O3FP7fG','walikelas','TKQ','Fauziyah'),
(15,'kanifah','$2y$10$3Aob1QpKo6ETZur2epix1.ReXMv3zUXO2TsvSX8NPc25/fia7NoMq','walikelas','TKQ','Kanifah'),
(16,'wulan_sari','$2y$10$Bd7Xx45PwNoD3aQ7yO7Y3OPKO/E2Hu1XBAUsMQIgYWZCXJf4BzwB2','walikelas','TKQ','Wulan Sari'),
(17,'rantika','$2y$10$fbczJM9zR4ZlqTzFhBYlW.zIQjKjmiWq2AB9PEJlvVq0b9HhcP3Py','walikelas','TKQ','Rantika'),
(18,'nursaidah','$2y$10$2X.fAceUHeL06zfgyYz.i.ofu5Ij/Q3JNnKsLJGmstCAlSnqAdpAC','walikelas','MDTU','Nursaidah'),
(19,'siti_amelia','$2y$10$rZWyajL44fB9wUSMYaifAOP2Eb50/sFVqqlQM2Cpi9mxmTHAElth2','walikelas','MDTU','Siti Amelia Nurul Rahmah'),
(20,'farha_hidayatullaela','$2y$10$arPs3ULjvbyveuM6xXXP/u..2WWwJ/mhtIajmfB/.N.SCqDp7l2jq','walikelas','MDTU','Farha Hidayatullaela'),
(21,'widiyaningrum','$2y$10$7NlwDop.hlzzdTSkpC3rPO9E0Af4Ax.wlsoT52Kss3UEFhTAOsy1G','admin',NULL,'Widiyaningrum'),
(22,'nurul_aeni','$2y$10$S.jY2X7wnZWAJdOSMDpQmeADfLphEIviYT7VMOLRwp1tQgTlaobKq','walikelas','SDIT','Nurul Aeni'),
(23,'ibnul_mubarok','$2y$10$8SrGFoGSGtVzM7i.O/BAFuikVJZrRg7TW87OMqFz6ddJnjs./KW8e','walikelas','SDIT','Ibnul Mubarok'),
(24,'yulianti','$2y$10$vlL58bQXw6Rk094HjU7RYuaYfuYGnhExPPvgYub1MmIOS.l3I5USS','walikelas','SDIT','Yulianti');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wa_queue`
--

DROP TABLE IF EXISTS `wa_queue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wa_queue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nis` varchar(20) DEFAULT NULL,
  `target` varchar(20) NOT NULL,
  `message` text NOT NULL,
  `status` enum('pending','sent') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wa_queue`
--

LOCK TABLES `wa_queue` WRITE;
/*!40000 ALTER TABLE `wa_queue` DISABLE KEYS */;
/*!40000 ALTER TABLE `wa_queue` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'sql_rqt'
--

--
-- Dumping routines for database 'sql_rqt'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-11 13:38:16
