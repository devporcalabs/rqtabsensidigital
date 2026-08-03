-- MySQL dump 10.13  Distrib 8.0.30, for Win64 (x86_64)
--
-- Host: localhost    Database: absensi_rqtemi
-- ------------------------------------------------------
-- Server version	8.0.30

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
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
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `absensi` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nis` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `waktu_masuk` datetime DEFAULT NULL,
  `keterangan` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'Hadir',
  `input_by` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `waktu_pulang` datetime DEFAULT NULL,
  `status_kehadiran` enum('Tepat Waktu','Terlambat') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'Tepat Waktu',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `absensi`
--

LOCK TABLES `absensi` WRITE;
/*!40000 ALTER TABLE `absensi` DISABLE KEYS */;
/*!40000 ALTER TABLE `absensi` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `absensi_guru`
--

DROP TABLE IF EXISTS `absensi_guru`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `absensi_guru` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nip` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `waktu_masuk` datetime DEFAULT NULL,
  `waktu_pulang` datetime DEFAULT NULL,
  `keterangan` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'Hadir',
  `status_kehadiran` enum('Tepat Waktu','Terlambat') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'Tepat Waktu',
  `input_by` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `absensi_guru`
--

LOCK TABLES `absensi_guru` WRITE;
/*!40000 ALTER TABLE `absensi_guru` DISABLE KEYS */;
/*!40000 ALTER TABLE `absensi_guru` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `guru`
--

DROP TABLE IF EXISTS `guru`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `guru` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nip` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `rfid_uid` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nama` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `no_hp` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `foto` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'default.jpg',
  `jabatan` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `face_embedding` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
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
INSERT INTO `guru` VALUES (1,'1901001','2715154701','Yati Supriati','81226096474','','guru_1901001_1785570767.jpg','Ketua Yayasan',NULL),(3,'2501002','2718946701','Ditya Dwi Edy S.A','81329320582',NULL,'guru_2501002_1785570816.jpg','Sekretaris Yayasan',NULL),(4,'2601003','2715369997','Muh. Dulkarnaen','83869868741',NULL,'guru_2601003_1785570816.jpg','Sekretaris Yayasan',NULL),(5,'2601004','2717544173','Nana Rusmana','82316643691',NULL,'guru_2601004_1785570816.jpg','Bendahara Yayasan',NULL),(6,'2602001','2719128573','Sri Anjani','85795661324',NULL,'guru_2602001_1785570816.jpg','Kepala Sekolah',NULL),(7,'2402002','2719243341','Eli Rizkiana','89659998939',NULL,'guru_2402002_1785570816.jpg','UMMI',NULL),(8,'2502003','2715099069','Fauziyah','89615230082',NULL,'guru_2502003_1785570816.jpg','Koordinator Kurikulum',NULL),(9,'2602004','2717830349','Kanifah','85848641043',NULL,'guru_2602004_1785570816.jpg','Koordinator Kesiswaan',NULL),(10,'2602005','2717775821','Wulan Sari','89507080854',NULL,'guru_2602005_1785570816.jpg','Guru',NULL),(11,'2602006','2716813485','Rantika','87892013370',NULL,'guru_2602006_1785570816.jpg','Guru',NULL),(12,'2503001','2719327373','Nurul Aeni','89634076898',NULL,'guru_2503001_1785570816.jpg','Kepala Sekolah',NULL),(13,'2503002','2719093197','Ibnul Mubarok','82262420282',NULL,'guru_2503002_1785570816.jpg','Koordinator UMMI',NULL),(14,'2603004','2716423565','Yulianti','8382366284',NULL,'guru_2603004_1785570816.jpg','Koordinator Kesiswaan',NULL),(15,'2504001','2715800893','Nursaidah','89520437374',NULL,'guru_2504001_1785570816.jpg','Koordinator Kurikulum',NULL),(16,'2604004','3278753917','Siti Amelia Nurul Rahmah','83874442299',NULL,'guru_2604004_1785570816.jpg','Guru',NULL),(17,'2604005','3279703613','Farha Hidayatullaela','83142267207',NULL,'guru_2604005_1785570816.jpg','Guru',NULL),(18,'2306001','2717891821','Widiyaningrum','89623300515',NULL,'guru_2306001_1785570816.jpg','Admin/Operator',NULL),(19,'2506002','2716772333','Nabila Azzahra','81997895708',NULL,'guru_2506002_1785570816.jpg','Bendahara Lembaga',NULL),(20,'2501005','2716688861','Jihan Fahira','85707060734',NULL,'guru_2501005_1785570816.jpg','Kebersihan',NULL),(21,'2601006','2715540845','Nurhasanah','83897201024',NULL,'guru_2601006_1785570816.jpg','Penjaga Kantin',NULL);
/*!40000 ALTER TABLE `guru` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `kantin_transaksi`
--

DROP TABLE IF EXISTS `kantin_transaksi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `kantin_transaksi` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nis` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `tipe` enum('debet','kredit') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'debet = belanja, kredit = top up',
  `nominal` int NOT NULL,
  `saldo_awal` int NOT NULL,
  `saldo_akhir` int NOT NULL,
  `keterangan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `waktu` datetime NOT NULL,
  `operator` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'username atau nama lengkap kasir/admin',
  PRIMARY KEY (`id`),
  KEY `idx_nis` (`nis`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kantin_transaksi`
--

LOCK TABLES `kantin_transaksi` WRITE;
/*!40000 ALTER TABLE `kantin_transaksi` DISABLE KEYS */;
/*!40000 ALTER TABLE `kantin_transaksi` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `kelas`
--

DROP TABLE IF EXISTS `kelas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `kelas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama_kelas` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nama_kelas` (`nama_kelas`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kelas`
--

LOCK TABLES `kelas` WRITE;
/*!40000 ALTER TABLE `kelas` DISABLE KEYS */;
INSERT INTO `kelas` VALUES (1,'MDTU'),(2,'SDIT'),(3,'TKQ'),(4,'TR');
/*!40000 ALTER TABLE `kelas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `kiosk_tokens`
--

DROP TABLE IF EXISTS `kiosk_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `kiosk_tokens` (
  `id` int NOT NULL,
  `token` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
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
/*!40000 ALTER TABLE `kiosk_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `libur_manual`
--

DROP TABLE IF EXISTS `libur_manual`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `libur_manual` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tanggal` date NOT NULL,
  `keterangan` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
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
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pengaturan` (
  `id` int NOT NULL,
  `nama_sekolah` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'Sekolah Kita',
  `jam_masuk` time DEFAULT '07:00:00',
  `jam_pulang_min` time DEFAULT '12:00:00',
  `mode_absen_pulang` tinyint DEFAULT '1',
  `timezone` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'Asia/Jakarta',
  `libur_pekanan` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `wa_mode` tinyint(1) DEFAULT '0',
  `wa_token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'sJ6yY7KFBVV2V5mqtSsV',
  `tg_bot_token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `pass_hapus` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `wa_api_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'https://api.fonnte.com/send',
  `pesan_masuk` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `pesan_pulang` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `logo_sekolah` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'logo_default.png',
  `smtp_host` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'smtp.gmail.com',
  `smtp_port` int DEFAULT '587',
  `smtp_user` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `smtp_pass` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `s1_masuk` time DEFAULT '07:00:00',
  `s1_pulang` time DEFAULT '12:00:00',
  `s2_masuk` time DEFAULT '12:30:00',
  `s2_pulang` time DEFAULT '17:00:00',
  `wajib_pulang` tinyint(1) DEFAULT '1',
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
INSERT INTO `pengaturan` VALUES (1,'Rumah Quran Temi','08:00:00','13:50:00',0,'Asia/Jakarta','Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',0,'BSXuvXKj2C2E9mfGzSj1','8373927632:AAF7i3UnoMjHIBMPo8BagRDn_8LCywO6RL8',NULL,'https://api.fonnte.com/send','Assalamualaikum. wr.wb. \r\nBpk/Ibu. Menginformasikan Ananda *[nama]* telah hadir di sekolah pukul *[jam]* [telat]. ini pesan otomatis saat ananda absen agar tidak membalas pesan ini.\r\n            ','Assalamualaikum.\r\nBpk/Ibu. Ananda  [nama] telah pulang dari sekolah pada [jam]. ini pesan otomatis saat ananda absen agar tidak membalas pesan ini.','logo_1785512911.png','smtp.gmail.com',587,'kirodevide001@gmail.com','gnry hkvp vgrl etvx','07:00:00','14:30:00','14:00:00','17:30:00',1,'07:00:00','11:00:00');
/*!40000 ALTER TABLE `pengaturan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `siswa`
--

DROP TABLE IF EXISTS `siswa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `siswa` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nis` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `rfid_uid` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nama` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `no_hp_ortu` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `telegram_chat_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `foto` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'default.jpg',
  `kelas` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `face_descriptor` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `sesi` enum('1','2','3') COLLATE utf8mb4_general_ci DEFAULT '1',
  `face_embedding` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `saldo` int DEFAULT '0',
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
INSERT INTO `siswa` VALUES (1,'22425.1.001','2717565741','ABDURRAHMAN HANIF','6282255886540','','','siswa_mdtu_1_1785547930.jpg','MDTU',NULL,'2',NULL,0),(2,'22425.1.004','2716982077','AHMAD RASYID AL FUADI','6282145760212','','','siswa_mdtu_2_1785547930.jpg','MDTU',NULL,'2',NULL,0),(3,'22526.1.002','2717531213','AL AYYUBI SYAFIQ HABIBI','6287763322730','','','siswa_mdtu_3_1785547930.jpg','MDTU',NULL,'2',NULL,0),(4,'22324.1.004','2716096541','ALEA ZAHRA','6283124929996','','','siswa_mdtu_4_1785547930.jpg','MDTU',NULL,'2',NULL,0),(5,'22526.1.003','2719168349','ALFARIZQI ABDUL JABBAR','62895413128004','','','siswa_mdtu_5_1785547930.jpg','MDTU',NULL,'2',NULL,0),(6,'22324.1.008','2715560077','ARSYILA ROMEESA FARZANA','6281284831568','','','siswa_mdtu_6_1785547930.jpg','MDTU',NULL,'2',NULL,0),(7,'22425.1.023','2718464413','ARYA AIMAR RAJENDRA','6282246246624','','','siswa_mdtu_7_1785547930.jpg','MDTU',NULL,'2',NULL,0),(8,'22526.1.005','2715802941','AZAM ZAIN HAMIZAN','6287830380090','','','siswa_mdtu_8_1785547931.jpg','MDTU',NULL,'2',NULL,0),(9,'22425.1.006','2716264829','AZKA ARFADHIA HAMDANI','6282219692492','','','siswa_mdtu_9_1785547931.jpg','MDTU',NULL,'2',NULL,0),(10,'22425.1.007','2718465421','AZKA RAFLI AUFA MAKSUM','6281324030004','','','siswa_mdtu_10_1785547931.jpg','MDTU',NULL,'2',NULL,0),(11,'22526.1.027','2717972893','BILAL RAMADHAN','62895321605242','','','siswa_mdtu_11_1785547931.jpg','MDTU',NULL,'2',NULL,0),(12,'22526.1.007','2718675437','DEANA SYAFA NUR ISLAM','6285353938977','','','siswa_mdtu_12_1785547931.jpg','MDTU',NULL,'2',NULL,0),(13,'22526.1.008','2715230957','DEVIYANI AGUSTIN','6285819758906','','','siswa_mdtu_13_1785547931.jpg','MDTU',NULL,'2',NULL,0),(14,'22425.1.008','2717457357','GHEA NATUSHA','6281319171009','','','siswa_mdtu_14_1785547931.jpg','MDTU',NULL,'2',NULL,0),(15,'22526.1.012','2718291085','HAFIDZOH AGHNIA KHANSA','6282298607490','','','siswa_mdtu_15_1785547931.jpg','MDTU',NULL,'2',NULL,0),(16,'22526.1.013','2719370989','HAFLAH HAFIDZAN AL HIDAYAT','6283154610562','','','siswa_mdtu_16_1785547931.jpg','MDTU',NULL,'2',NULL,0),(17,'22425.1.009','2719245933','HILYA NAFISAH','628212047253','','','siswa_mdtu_17_1785547931.jpg','MDTU',NULL,'2',NULL,0),(18,'22425.1.010','2718566445','IRFAN FADHI','6285222530691','','','siswa_mdtu_18_1785547931.jpg','MDTU',NULL,'2',NULL,0),(19,'22324.1.018','2718383213','KAZEN SHABIL ALDEBARAN','6287727020568','','','siswa_mdtu_19_1785547931.jpg','MDTU',NULL,'2',NULL,0),(20,'22324.1.011','2715506077','MARYAM','62895807041919','','','siswa_mdtu_20_1785547931.jpg','MDTU',NULL,'2',NULL,0),(21,'22425.1.011','2716524189','MAURA ELVIRA SYAUQIA','6289515040972','','','siswa_mdtu_21_1785547931.jpg','MDTU',NULL,'2',NULL,0),(22,'22526.1.015','2718755709','MEIGA PUTRI HANEDI','6281311127679','','','siswa_mdtu_22_1785547931.jpg','MDTU',NULL,'2',NULL,0),(23,'22526.1.016','2716776749','MOHAMAD YUUKI PRADIPTA','6282311770341','','','siswa_mdtu_23_1785547931.jpg','MDTU',NULL,'2',NULL,0),(24,'22526.1.018','2715995933','MUHAMMAD AZKA AL FA\'IZ','6287729264924','','','siswa_mdtu_24_1785547931.jpg','MDTU',NULL,'2',NULL,0),(25,'22526.1.020','2717241469','MUHAMMAD DAFFA ADDIEN','6281313347986','','','siswa_mdtu_25_1785547931.jpg','MDTU',NULL,'2',NULL,0),(26,'22526.1.021','2718471997','MUHAMMAD FAHRI NURHAKIM','6281322313698','','','siswa_mdtu_26_1785547931.jpg','MDTU',NULL,'2',NULL,0),(27,'22324.1.014','2716057453','MUHAMMAD HAFIZH TRIYANT ANARGYA','6282120806947','','','siswa_mdtu_27_1785547931.jpg','MDTU',NULL,'2',NULL,0),(28,'22425.1.013','3279322189','MUHAMMAD NURDAFFA AKBAR','6282128512167','','','siswa_mdtu_28_1785547931.jpg','MDTU',NULL,'2',NULL,0),(29,'22425.1.014','3277960269','MUHAMMAD RIDWAN SAFI\'I','6289647287610','','','siswa_mdtu_29_1785547931.jpg','MDTU',NULL,'2',NULL,0),(30,'22425.1.015','3281176509','MUHAMMAD YAZID AL HADROMI','6282218578119','','','2425.1.015_1785548107.jpg','MDTU',NULL,'2',NULL,0),(31,'22324.1.013','3279566989','NAJMATU ZHOHIRO PUTRI PRAYITNO','62895705125552','','','siswa_mdtu_31_1785547932.jpg','MDTU',NULL,'2',NULL,0),(32,'22324.1.016','3279932445','NINO RIZKY ALFATHIR','628995777112','','','siswa_mdtu_32_1785547932.jpg','MDTU',NULL,'2',NULL,0),(33,'22526.1.023','1234567890','RATU BILQIS AZZAHRA','6282220225505','','','siswa_mdtu_33_1785547932.jpg','MDTU',NULL,'2',NULL,0),(34,'22526.1.024','3280745181','REYHAN TRIYADI','6287777947585','','','siswa_mdtu_34_1785547932.jpg','MDTU',NULL,'2',NULL,0),(35,'22425.1.018','2716158909','SABIYA ALFATHUNNISSA','6285624471699','','','siswa_mdtu_35_1785547932.jpg','MDTU',NULL,'2',NULL,0),(36,'22324.1.010','2717116509','SYIFA DWI TIFANI','6285838254944','','','siswa_mdtu_36_1785547932.jpg','MDTU',NULL,'2',NULL,0),(37,'22526.1.026','2717314701','SYIFA NURUL HASANAH','6281298022414','','','siswa_mdtu_37_1785547932.jpg','MDTU',NULL,'2',NULL,0),(38,'22425.1.020','2719482733','TRISTAN PRATAMA RIZQULLAH','6289528603392','','','siswa_mdtu_38_1785547932.jpg','MDTU',NULL,'2',NULL,0),(39,'22425.1.022','2716968013','WISNU ADI KESUMA','6282120855223','','','siswa_mdtu_39_1785547932.jpg','MDTU',NULL,'2',NULL,0),(40,'22324.1.017','2716758365','ZAKIYYA TALITA SAKHI HABIBI','6287707060734','','','siswa_mdtu_40_1785547932.jpg','MDTU',NULL,'2',NULL,0),(41,'22627.1.001','2718258445','ALMEIRA BENAZIR MANAF','62895385313008','','','siswa_mdtu_41_1785547930.jpg','MDTU',NULL,'2',NULL,0),(42,'22627.1.002','2716469853','AXCELLO RAFIANDRA SYAHPUTRA','6283804679509','','','siswa_mdtu_42_1785547930.jpg','MDTU',NULL,'2',NULL,0),(43,'22627.1.003','2716801293','AYSILA HUSNA','6285352876410','','','siswa_mdtu_43_1785547930.jpg','MDTU',NULL,'2',NULL,0),(44,'22627.1.004','2717773085','CAHAYA INDAH DARA','6285781125668','','','siswa_mdtu_44_1785547931.jpg','MDTU',NULL,'2',NULL,0),(45,'22627.1.005','2717640285','GHOSSANY MIR\'ATUL IZZAH','6285224070095','','','siswa_mdtu_45_1785547931.jpg','MDTU',NULL,'2',NULL,0),(46,'22627.1.006','2715191613','HAFIZHATUL HASNA PUTRI ADELIA','6287828610177','','','siswa_mdtu_46_1785547931.jpg','MDTU',NULL,'2',NULL,0),(47,'22627.1.007','2717131757','HERZA KENZO ALVIANO','6287720710953','','','siswa_mdtu_47_1785547931.jpg','MDTU',NULL,'2',NULL,0),(48,'22627.1.008','2716635069','KEIKO RAFIQ ARSYAD','6282240185774','','','siswa_mdtu_48_1785547931.jpg','MDTU',NULL,'2',NULL,0),(49,'22627.1.009','2716951837','KIRANA AQILLA','6285199229214','','','siswa_mdtu_49_1785547931.jpg','MDTU',NULL,'2',NULL,0),(50,'22627.1.010','2716386989','MALIQ AL RASYID','6289529449085','','','2627.1.010_1785548076.jpg','MDTU',NULL,'2',NULL,0),(51,'22627.1.011','2716026477','MAULIDA ZULFA ELZUHARA','628121459427','','','siswa_mdtu_51_1785547931.jpg','MDTU',NULL,'2',NULL,0),(52,'22627.1.012','2717455229','MOH. AFFSAN SAPUTRA','6287894263121','','','siswa_mdtu_52_1785547931.jpg','MDTU',NULL,'2',NULL,0),(53,'22627.1.013','3279616237','MUSYRIF MUHAMMAD RIZQIANSYAH','6289661061526','','','siswa_mdtu_53_1785547931.jpg','MDTU',NULL,'2',NULL,0),(54,'22627.1.014','3281686829','NARESWARA PUTRA BAHARI','6282320257930','','','siswa_mdtu_54_1785547932.jpg','MDTU',NULL,'2',NULL,0),(55,'22627.1.015','3280955725','NASHA KIREI AZZAHRA','6281564948899','','','siswa_mdtu_55_1785547932.jpg','MDTU',NULL,'2',NULL,0),(56,'22627.1.016','2716773309','SEFTI MARWAH AZAHRA','628960894005','','','siswa_mdtu_56_1785547932.jpg','MDTU',NULL,'2',NULL,0),(57,'22627.1.017','2717777533','YASBIH QUUINSYAH SETIAWAN','6289513018292','','','siswa_mdtu_57_1785547932.jpg','MDTU',NULL,'2',NULL,0),(58,'22627.1.018','2716968029','ZAFIN AZHARI','6289675812465','','','siswa_mdtu_58_1785547932.jpg','MDTU',NULL,'2',NULL,0),(59,'22425.1.012','2716843037','MUHAMMAD AZZAM EL-SYAUQI','6282316367755','','','2425.1.012_1785548050.jpg','MDTU',NULL,'2',NULL,0),(64,'12526.1.001','2719057053','Abil Allbiansyah ','6283825947820','','','siswa_sdit_64_1785555575.jpg','SDIT',NULL,'1',NULL,0),(65,'12526.1.002','2717850733','Alvino Akmal Nurhidayat','6289824111811','','','siswa_sdit_65_1785555575.jpg','SDIT',NULL,'1',NULL,0),(66,'12526.1.003','2715486205','Cantika Nur Anindya','6281222741547','','','siswa_sdit_66_1785555575.jpg','SDIT',NULL,'1',NULL,0),(67,'12526.1.004','2717320141','Fakhroh Noftieleven Faroohah','6285321630432','','','siswa_sdit_67_1785555575.jpg','SDIT',NULL,'1',NULL,0),(68,'12526.1.005','2718246317','Fanisa Ashalina','6289693837081','','','siswa_sdit_68_1785555576.jpg','SDIT',NULL,'1',NULL,0),(69,'12526.1.006','2715031501','Hulwah Qonita Raniah','628977058618','','','siswa_sdit_69_1785555576.jpg','SDIT',NULL,'1',NULL,0),(70,'12526.1.007','2718336461','Muhammad Baba Assamasi','62895377422654','','','siswa_sdit_70_1785555576.jpg','SDIT',NULL,'1',NULL,0),(71,'12526.1.008','2716454045','Radeya Syairazy Sukmana','6285797487306','','','siswa_sdit_71_1785555576.jpg','SDIT',NULL,'1',NULL,0),(72,'12627.1.001','2716085901','Adnan Fa\'iz Al Fadhi','6289662202694','','','siswa_sdit_72_1785555575.jpg','SDIT',NULL,'1',NULL,0),(73,'12627.1.002','2718985501','Aishwa Nayyara Husna','6282145799498','','','siswa_sdit_73_1785555575.jpg','SDIT',NULL,'1',NULL,0),(74,'12627.1.003','2719130317','Arjuna Fatih Al-Hawwari','6285649029213','','','siswa_sdit_74_1785555575.jpg','SDIT',NULL,'1',NULL,0),(75,'12627.1.004','2718237037','Arsyad Alghifari','6287876880589','','','siswa_sdit_75_1785555575.jpg','SDIT',NULL,'1',NULL,0),(76,'12627.1.005','2718055117','Arumi Nasya Razeeta','6285813943333','','','siswa_sdit_76_1785555575.jpg','SDIT',NULL,'1',NULL,0),(77,'12627.1.006','2715793005','Dede Khaerul Anam','6285786034640','','','siswa_sdit_77_1785555575.jpg','SDIT',NULL,'1',NULL,0),(78,'12627.1.007','2717667341','Lusiyana Intan','6282290576498','','','siswa_sdit_78_1785555576.jpg','SDIT',NULL,'1',NULL,0),(79,'12627.1.008','2717030477','Muhammad Daffa Al-Ghifari','6281214505511','','','siswa_sdit_79_1785555576.jpg','SDIT',NULL,'1',NULL,0),(80,'12627.1.009','2717096317','Muhammad Rifqi Sujahilman','6282115609442','','','siswa_sdit_80_1785555576.jpg','SDIT',NULL,'1',NULL,0),(81,'12627.1.010','2716737117','Muhammad Wafiyul Huda','6285743286697','','','siswa_sdit_81_1785555576.jpg','SDIT',NULL,'1',NULL,0),(82,'12627.1.011','2716251197','Niko Julian Pratama','6282117301795','','','siswa_sdit_82_1785555576.jpg','SDIT',NULL,'1',NULL,0),(83,'12627.1.012','2718996669','Queensha Arunika Elshanum','628988443150','','','siswa_sdit_83_1785555576.jpg','SDIT',NULL,'1',NULL,0),(84,'12627.1.013','2716694157','Reza Adinata','6287829251515','','','siswa_sdit_84_1785555576.jpg','SDIT',NULL,'1',NULL,0),(85,'12627.1.014','2716268045','Riska Tiara Maharani','628999961297','','','siswa_sdit_85_1785555576.jpg','SDIT',NULL,'1',NULL,0),(86,'12627.1.015','2716640653','Shabrina Azzura Mafazah','6289661717973','','','siswa_sdit_86_1785555576.jpg','SDIT',NULL,'1',NULL,0),(87,'12627.1.016','2717849901','Shaka Alfarizqi','6281321143593','','','siswa_sdit_87_1785555576.jpg','SDIT',NULL,'1',NULL,0),(88,'12627.1.017','2716069597','Sultan Raffasya','6281321043058','','','siswa_sdit_88_1785555576.jpg','SDIT',NULL,'1',NULL,0),(89,'12627.1.018','2716784653','Tania Yoni Putri','6281912998018','','','siswa_sdit_89_1785555576.jpg','SDIT',NULL,'1',NULL,0),(90,'12627.1.019','2718712717','Ulya Hana Mahdyah','6281322597930','','','siswa_sdit_90_1785555576.jpg','SDIT',NULL,'1',NULL,0),(91,'32627.1.001','2716807085','ADIBAH SRI RAHAYU','6285320651863','','','siswa_tkq_91_1785568911.jpg','TKQ',NULL,'3',NULL,0),(92,'32627.1.002','2718024061','AHMAD AL-FATIH','6281324204228','','','siswa_tkq_92_1785568912.jpg','TKQ',NULL,'3',NULL,0),(93,'32627.1.003','2716229277','AKMAL HAFIDZ AL-DZIKRI','6288211333052','','','siswa_tkq_93_1785568912.jpg','TKQ',NULL,'3',NULL,0),(94,'32627.1.004','2719128445','ALDEVARO REYHAN NANDANA','6289605527822','','','siswa_tkq_94_1785568912.jpg','TKQ',NULL,'3',NULL,0),(95,'32627.1.005','2715186557','ALESHA ZAMEENA NASLA','6281288145827','','','siswa_tkq_95_1785568912.jpg','TKQ',NULL,'3',NULL,0),(96,'32627.1.006','2717177277','ALPIN NURHADI','6285691837487','','','siswa_tkq_96_1785568912.jpg','TKQ',NULL,'3',NULL,0),(97,'32627.1.007','2717778813','ALTAN ALVARENDRA PRAYITNO','62895705125552','','','siswa_tkq_97_1785568912.jpg','TKQ',NULL,'3',NULL,0),(98,'32627.1.008','3277501069','ANANTA PRATAMA','6282125158386','','','siswa_tkq_98_1785568912.jpg','TKQ',NULL,'3',NULL,0),(99,'32627.1.009','3280993373','ARBAIN IZZAN ANNAWAWI','6281316421078','','','siswa_tkq_99_1785568912.jpg','TKQ',NULL,'3',NULL,0),(100,'32627.1.010','3281754445','ARRAYA QIANDRA AUDREANA','6281321143593','','','siswa_tkq_100_1785568912.jpg','TKQ',NULL,'3',NULL,0),(101,'32627.1.011','3278097997','ARSAKHA HIROSHI','6282311770341','','','siswa_tkq_101_1785568912.jpg','TKQ',NULL,'3',NULL,0),(102,'32627.1.012','3278575885','ARZU DWI SYAQILLA','6282129521705','','','siswa_tkq_102_1785568912.jpg','TKQ',NULL,'3',NULL,0),(103,'32627.1.013','3277435693','AZKIA ZAHIDA NURFAUZIAH','62896969399000','','','siswa_tkq_103_1785568912.jpg','TKQ',NULL,'3',NULL,0),(104,'32627.1.014','3279379117','CYRINDA BELLA','6282123974326','','','siswa_tkq_104_1785568912.jpg','TKQ',NULL,'3',NULL,0),(105,'32627.1.015','2715274637','DANEEN GIANI HUTAMA','628996727724','','','siswa_tkq_105_1785568912.jpg','TKQ',NULL,'3',NULL,0),(106,'32627.1.016','2717399037','DANENDRA DWI PRANATA','628211311505','','','siswa_tkq_106_1785568912.jpg','TKQ',NULL,'3',NULL,0),(107,'32627.1.017','2717114637','FARKHA AMIELIA','62895357033555','','','siswa_tkq_107_1785568912.jpg','TKQ',NULL,'3',NULL,0),(108,'32627.1.018','2719034413','FATIMAH DZIHNI HASANAH','628975489228','','','siswa_tkq_108_1785568912.jpg','TKQ',NULL,'3',NULL,0),(109,'32627.1.019','2719104781','GIBRAN MALIK ALFARIZKY','6289661173587','','','siswa_tkq_109_1785568912.jpg','TKQ',NULL,'3',NULL,0),(110,'32627.1.020','2718054909','GWEEN ALETA PUTRI SAMPURNA','6281224345982','','','siswa_tkq_110_1785568912.jpg','TKQ',NULL,'3',NULL,0),(111,'32627.1.021','2716552557','IBRAHIM MISHARY AHZA NASUTION','6282289594616','','','siswa_tkq_111_1785568912.jpg','TKQ',NULL,'3',NULL,0),(112,'32627.1.022','2715484973','ISMATUL INAYAH','6282315917102','','','siswa_tkq_112_1785568912.jpg','TKQ',NULL,'3',NULL,0),(113,'32627.1.023','2718581213','KAHAYANG GI AMRILA ALULA','6289530053006','','','siswa_tkq_113_1785568912.jpg','TKQ',NULL,'3',NULL,0),(114,'32627.1.024','2718433549','KHAIRA HUMAIRA AFSHEENA','6282315793539','','','siswa_tkq_114_1785568912.jpg','TKQ',NULL,'3',NULL,0),(115,'32627.1.025','2717063885','MEIKHA ADRIANA','6283869513184','','','siswa_tkq_115_1785568913.jpg','TKQ',NULL,'3',NULL,0),(116,'32627.1.026','2716295613','MUHAMMAD AL FATIH HABIBI','6287763322730','','','siswa_tkq_116_1785568913.jpg','TKQ',NULL,'3',NULL,0),(117,'32627.1.027','2716633597','MUHAMMAD AL-FATIH ROISUL ISLAM','6289523851944','','','siswa_tkq_117_1785568913.jpg','TKQ',NULL,'3',NULL,0),(118,'32627.1.028','2716007709','MUHAMMAD ATHARIZZ CALIEF','6281188055966','','','siswa_tkq_118_1785568913.jpg','TKQ',NULL,'3',NULL,0),(119,'32627.1.029','2717013629','MUHAMMAD AZKA GHIFARI','6282115797787','','','siswa_tkq_119_1785568913.jpg','TKQ',NULL,'3',NULL,0),(120,'32627.1.030','2716028013','MUHAMMAD SYAUQI RAMADHAN','62895337992391','','','siswa_tkq_120_1785568913.jpg','TKQ',NULL,'3',NULL,0),(121,'32627.1.031','2717451517','NANDITO','6289662202694','','','siswa_tkq_121_1785568913.jpg','TKQ',NULL,'3',NULL,0),(122,'32627.1.032','2716566141','RAFA ALFARIZKI','6282129521705','','','siswa_tkq_122_1785568913.jpg','TKQ',NULL,'3',NULL,0),(123,'32627.1.033','2716840765','REYHAN ALFA RISQI','6287717648645','','','siswa_tkq_123_1785568913.jpg','TKQ',NULL,'3',NULL,0),(124,'32627.1.034','2716702205','SABRINA KHAIRA NAFISHA','6285624471699','','','siswa_tkq_124_1785568913.jpg','TKQ',NULL,'3',NULL,0),(125,'32627.1.035','2718833965','SAYHAN FAIZ MUBARAK','6281284831568','','','siswa_tkq_125_1785568913.jpg','TKQ',NULL,'3',NULL,0),(126,'32627.1.036','2718301021','SYAFIQ ABDURRAHMAN','6283823231318','','','siswa_tkq_126_1785568913.jpg','TKQ',NULL,'3',NULL,0),(127,'32627.1.037','2716005021','TIARA NUR ROHMAN','62895635869385','','','siswa_tkq_127_1785568913.jpg','TKQ',NULL,'3',NULL,0),(128,'32627.1.038','2715357341','UGI LIONAFAH','6283894507370','','','siswa_tkq_128_1785568913.jpg','TKQ',NULL,'3',NULL,0),(129,'32627.1.039','2715721901','UMAR AS-SIDIQ WINATA','6281272235795','','','siswa_tkq_129_1785568913.jpg','TKQ',NULL,'3',NULL,0),(130,'32627.1.040','2715974797','ZADA BADRA GUMELAR','6287848267592','','','siswa_tkq_130_1785568913.jpg','TKQ',NULL,'3',NULL,0),(131,'32627.1.041','2719444877','ZALINA HUMAIRA','6281318000034','','','siswa_tkq_131_1785568913.jpg','TKQ',NULL,'3',NULL,0),(132,'32526.1.001','2718364909','ABDURRAHMAN IHSAN','6282255886540','','','siswa_tkq_132_1785568911.jpg','TKQ',NULL,'3',NULL,0),(133,'32526.1.002','2715470221','ABHINAYA HENPRI CAHYO ANUGRAH','6283178714498','','','siswa_tkq_133_1785568911.jpg','TKQ',NULL,'3',NULL,0),(134,'32526.1.004','2715831677','AISYAH SIFA ALINA ROHMAN','6282130215966','','','siswa_tkq_134_1785568912.jpg','TKQ',NULL,'3',NULL,0),(135,'32526.1.005','2717781597','AKMAL HAQI AMRULLAH','6287717685479','','','siswa_tkq_135_1785568912.jpg','TKQ',NULL,'3',NULL,0),(136,'32526.1.006','2715285837','ALLEXANDRIA SALSA SABILLAH','6287736446444','','','siswa_tkq_136_1785568912.jpg','TKQ',NULL,'3',NULL,0),(137,'32526.1.007','3279116813','ARSYA RAHMAN ARRASYIED','6281324541574','','','siswa_tkq_137_1785568912.jpg','TKQ',NULL,'3',NULL,0),(138,'32526.1.011','2719125533','GAIATRI DAMARA HAYU','6287722425099','','','siswa_tkq_138_1785568912.jpg','TKQ',NULL,'3',NULL,0),(139,'32425.01.019','2716092173','HANA IMROATUZZAKIYYAH','6289610079122','','','siswa_tkq_139_1785568912.jpg','TKQ',NULL,'3',NULL,0),(140,'32526.1.013','2715967821','LADY MIKAYLA YABANI','6282317814845','','','siswa_tkq_140_1785568912.jpg','TKQ',NULL,'3',NULL,0),(141,'32526.1.015','2717064413','LUTHFAN ANKA RAMADHAN','6281220061455','','','siswa_tkq_141_1785568912.jpg','TKQ',NULL,'3',NULL,0),(142,'32526.1.016','2719160125','MUHAMMAD NAZRIL BILLAR SETIAWAN','6281222158153','','','siswa_tkq_142_1785568913.jpg','TKQ',NULL,'3',NULL,0),(143,'32526.1.017','2715375037','MUHAMMAD REYNAND AL LUTHFI','6285813943333','','','siswa_tkq_143_1785568913.jpg','TKQ',NULL,'3',NULL,0),(144,'32526.1.018','2716367853','MUHAMMAD ZAYDAN BADAWI','6282217486552','','','siswa_tkq_144_1785568913.jpg','TKQ',NULL,'3',NULL,0),(145,'32526.1.020','2716418669','MUTIARA KAYREEN SUNANDAR','6285864128915','','','siswa_tkq_145_1785568913.jpg','TKQ',NULL,'3',NULL,0),(146,'32526.1.021','2718240749','SHAHIA SIHFA HASRI','62811201439','','','siswa_tkq_146_1785568913.jpg','TKQ',NULL,'3',NULL,0),(147,'32526.1.022','2718557245','SHAKIRA KAHISA SAFWANA','6287828959857','','','siswa_tkq_147_1785568913.jpg','TKQ',NULL,'3',NULL,0),(148,'32526.1.023','2717410317','SITI MELY YANI','6282321077335','','','siswa_tkq_148_1785568913.jpg','TKQ',NULL,'3',NULL,0),(149,'32526.1.024','2715798829','SITI NUR HASANAH','6283871201271','','','siswa_tkq_149_1785568913.jpg','TKQ',NULL,'3',NULL,0),(150,'32526.1.025','2716716157','SYAKIRA FAHIRA RAMADHANI','6281386181607','','','siswa_tkq_150_1785568913.jpg','TKQ',NULL,'3',NULL,0),(151,'42526.1.001','2718685405','Ayatul Husna Adelia Putri','6287828610177',NULL,NULL,'siswa_tr_425261001_1785571531.jpg','TR',NULL,'1',NULL,0),(152,'42425.01.00','2718226141','Fiorenza Amanda','6282127941204',NULL,NULL,'siswa_tr_424250100_1785571531.jpg','TR',NULL,'1',NULL,0),(153,'42526.1.006','2715514477','Siti Vera Fitriyani','6282118108761',NULL,NULL,'siswa_tr_425261006_1785571531.jpg','TR',NULL,'1',NULL,0),(154,'42627.1.001','2717439277','Abdul Naufal Alhadid','6285295866584',NULL,NULL,'siswa_tr_426271001_1785571531.jpg','TR',NULL,'1',NULL,0),(155,'42627.1.002','2715941709','Alsafani','6287876880589',NULL,NULL,'siswa_tr_426271002_1785571531.jpg','TR',NULL,'1',NULL,0),(156,'42627.1.003','2718245885','Elvira Balqis Ali','6282319800894',NULL,NULL,'siswa_tr_426271003_1785571531.jpg','TR',NULL,'1',NULL,0),(157,'42627.1.004','2717057229','Zahira Hanifah Liljannah','6282116201490',NULL,NULL,'siswa_tr_426271004_1785571531.jpg','TR',NULL,'1',NULL,0);
/*!40000 ALTER TABLE `siswa` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `role` enum('admin','walikelas','piket','kantin') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `kelas_diampu` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nama_lengkap` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'admin','$2y$10$cD3iHhZ9MuNRHlHd0VW5UOeWh.GFcFn1JjbDcFwZwA1ILoN0A22Ra','admin',NULL,'Solahudin Al Ayubih, S.Kom, Gr.');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wa_queue`
--

DROP TABLE IF EXISTS `wa_queue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wa_queue` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nis` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `target` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `status` enum('pending','sent') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
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
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-08-03 21:37:46
