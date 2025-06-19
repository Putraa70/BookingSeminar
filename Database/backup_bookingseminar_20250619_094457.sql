-- MySQL dump 10.13  Distrib 8.0.30, for Win64 (x86_64)
--
-- Host: localhost    Database: bookingseminar
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
-- Table structure for table `approvals`
--

DROP TABLE IF EXISTS `approvals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `approvals` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_seminar` int NOT NULL,
  `id_user` int NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `komentar` text,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `id_seminar` (`id_seminar`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `approvals_ibfk_1` FOREIGN KEY (`id_seminar`) REFERENCES `seminars` (`id`),
  CONSTRAINT `approvals_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `approvals`
--

LOCK TABLES `approvals` WRITE;
/*!40000 ALTER TABLE `approvals` DISABLE KEYS */;
INSERT INTO `approvals` VALUES (3,2,7,'approved','','2025-06-18 23:29:04','2025-06-18 23:29:31'),(4,2,10,'rejected','saya sibuk','2025-06-18 23:29:04','2025-06-18 23:29:55'),(5,3,9,'approved','','2025-06-19 16:27:08','2025-06-19 16:28:04'),(6,3,11,'rejected','maless','2025-06-19 16:27:08','2025-06-19 16:28:33'),(7,4,7,'approved','','2025-06-19 16:30:46','2025-06-19 16:31:04'),(8,4,12,'approved','','2025-06-19 16:30:46','2025-06-19 16:31:28');
/*!40000 ALTER TABLE `approvals` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mahasiswa_dosen`
--

DROP TABLE IF EXISTS `mahasiswa_dosen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mahasiswa_dosen` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_mahasiswa` int NOT NULL,
  `id_dosen` int NOT NULL,
  `jenis_dosen` enum('pembimbing','penguji') NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_mahasiswa` (`id_mahasiswa`),
  KEY `id_dosen` (`id_dosen`),
  CONSTRAINT `mahasiswa_dosen_ibfk_1` FOREIGN KEY (`id_mahasiswa`) REFERENCES `users` (`id`),
  CONSTRAINT `mahasiswa_dosen_ibfk_2` FOREIGN KEY (`id_dosen`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mahasiswa_dosen`
--

LOCK TABLES `mahasiswa_dosen` WRITE;
/*!40000 ALTER TABLE `mahasiswa_dosen` DISABLE KEYS */;
INSERT INTO `mahasiswa_dosen` VALUES (1,4,2,'pembimbing'),(2,4,3,'penguji'),(5,6,2,'pembimbing'),(6,6,3,'penguji'),(19,13,7,'pembimbing'),(20,13,10,'penguji'),(21,14,9,'pembimbing'),(22,14,11,'penguji'),(23,15,7,'pembimbing'),(24,15,12,'penguji'),(25,16,9,'pembimbing'),(26,16,10,'penguji');
/*!40000 ALTER TABLE `mahasiswa_dosen` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `seminars`
--

DROP TABLE IF EXISTS `seminars`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `seminars` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_user` int NOT NULL,
  `judul` varchar(255) NOT NULL,
  `tanggal` date NOT NULL,
  `waktu_mulai` time NOT NULL,
  `waktu_selesai` time NOT NULL,
  `deskripsi` text NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `seminars_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `seminars`
--

LOCK TABLES `seminars` WRITE;
/*!40000 ALTER TABLE `seminars` DISABLE KEYS */;
INSERT INTO `seminars` VALUES (2,13,'Sistem Booking Ruangan Seminar Berbasis Web','2025-06-18','08:00:00','10:00:00','Penelitian ini membahas perancangan dan implementasi sistem booking ruangan seminar berbasis web yang bertujuan untuk memudahkan proses pengajuan, persetujuan, dan penjadwalan seminar di lingkungan kampus. Sistem ini memungkinkan mahasiswa untuk mengajukan pemesanan ruangan seminar secara online, yang kemudian diverifikasi dan disetujui oleh dosen pembimbing serta dosen penguji. Dengan adanya sistem ini, proses administrasi seminar menjadi lebih efisien, transparan, dan terorganisir.','rejected','2025-06-18 23:29:04','2025-06-18 23:29:55'),(3,14,'Website berbasis gpt','2025-06-19','07:00:00','19:00:00','mohon acc pakk','rejected','2025-06-19 16:27:08','2025-06-19 16:28:33'),(4,15,'absen berbasis website dan iot','2025-06-19','09:00:00','10:00:00','acc bukk','approved','2025-06-19 16:30:46','2025-06-19 16:31:28');
/*!40000 ALTER TABLE `seminars` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('peserta','dosen_pembimbing','dosen_penguji','admin') NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `npm` varchar(20) DEFAULT NULL,
  `prodi` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (2,'Pak Ridho Solehurohman M.Mat','ridho@unila.com','$2y$10$i3t3GgSkcuKFTDdokbhmceRCyxcngJHB551Ifs3A7gdluNkSwvi76','dosen_pembimbing','2025-06-18 12:14:26',NULL,NULL),(3,'Dr. Aristoteles, S.Si., M.Si','aris@unila.com','$2y$10$a.yHwt3E0jdXsH78LrvFie70axm.Q1rF30zPvnby3pHRoDQzoAqRW','dosen_penguji','2025-06-18 12:15:34',NULL,NULL),(4,'admin','admin@unila.com','$2y$10$FZG4ytGi7Fsd0RHDGai9sOwyqvXi20HUeJ/wC.Ng8qMEaHa3Nfe9e','admin','2025-06-18 20:34:10','098',NULL),(6,'Putra','putra@unila.com','$2y$10$HhqYvQIRyJ7nywZ4vufVwuxPSRHPAcgkGR/IcGL.c80uOwnnmd2vi','peserta','2025-06-18 20:42:41','2317051098','ilmu komputer'),(7,'Anie Rose Irawati, S.T., M.Cs.','anie@unila.com','$2y$10$Lx7VhfJVe88gAYtrMEvIhOvF7MbLA2OX85N7g0Kgw83fAyrISw/IO','dosen_pembimbing','2025-06-18 22:03:47',NULL,NULL),(8,'Bambang Hermanto, S.Kom., M.Cs.','bambang@unila.com','$2y$10$3fa.fXQBHvhdrLheauVlPuAAktCCsSu.eIf.BqkCBE/g3vD4D/rlK','dosen_pembimbing','2025-06-18 22:04:12',NULL,NULL),(9,'Didik Kurniawan, S.T., M.T.','didik@unila.com','$2y$10$/buoGvk2b7kHJ.F4o/7E4OwjxMq6zvGokz/h/05q74MKQkl0yyFYW','dosen_pembimbing','2025-06-18 22:04:31',NULL,NULL),(10,'Favorisen R. Lumbanraja, S.Kom., M.Si., Ph.D.','favo@unila.com','$2y$10$ZX/MyaqRuoAxqH6FAcgMFOD8lxI2VpRpoQW8tcoFVH8Cp69F0tq.y','dosen_penguji','2025-06-18 22:04:50',NULL,NULL),(11,'Tristiyanto, Ph.D.','tris@unila.com','$2y$10$hpssVwWTnKvCvsL/dELKpeAo3ghTwcTPGnwbw6V6MhFSl1mYWDJja','dosen_penguji','2025-06-18 22:05:11',NULL,NULL),(12,'Ossy Dwi Endah Wulansari, S.Si., M.T.','ossy@unila.com','$2y$10$LIIbJV98m43i3zpOKVaDXu76TeDaiKBXND9HKyQegRgx751r52su.','dosen_penguji','2025-06-18 22:05:41',NULL,NULL),(13,'arnetha eka purwati','arnetha@unila.com','$2y$10$yjDmYsh0FAlll/qWNblBUeHVCJH1gI2eIXN7ugtrFSPBX/ubf3gvO','peserta','2025-06-18 22:18:08','2317051023','Ilmu komputer'),(14,'Swasita','sita@unila.com','$2y$10$q8PAORXCzCnxVUE0Y9yWHem5T7uTJLFPy5iNvWgsJJC5TA34A85Qq','peserta','2025-06-19 16:24:48','2317051018','Ilmu komputer'),(15,'meyta','meyta@unila.com','$2y$10$Ko2y.g2nZKHwMnnR5q3iMOdT7CYAOFpGojvMntG701vSxQh8jWsxq','peserta','2025-06-19 16:29:22','2357051006','Ilmu komputer'),(16,'mubarok al ilhami','mubarok@yahoo.com','$2y$10$PFjv3FT3W/X31OlfE.Psuu/65/deow1.wb/nk1Zn2zjJsVyFXjC5C','peserta','2025-06-19 16:42:31','2317051080','ilmu komputer');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-06-19 16:44:57
