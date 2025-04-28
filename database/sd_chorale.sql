-- MySQL dump 10.13  Distrib 8.0.41, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: sd_chorale
-- ------------------------------------------------------
-- Server version	5.5.5-10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `accessories`
--

DROP TABLE IF EXISTS `accessories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `accessories` (
  `deco_id` int(11) NOT NULL AUTO_INCREMENT,
  `deco_name` varchar(50) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `condition` enum('GOOD','BAD') DEFAULT 'GOOD',
  PRIMARY KEY (`deco_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `accessories`
--

LOCK TABLES `accessories` WRITE;
/*!40000 ALTER TABLE `accessories` DISABLE KEYS */;
INSERT INTO `accessories` VALUES (1,'Bamboo Bilao',4,'GOOD'),(2,'Metal Hoop',1,'GOOD'),(3,'Vine Decoration',2,'GOOD'),(4,'Portable Booth',1,'GOOD'),(5,'Tarpaulin',1,'GOOD');
/*!40000 ALTER TABLE `accessories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `clothing`
--

DROP TABLE IF EXISTS `clothing`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `clothing` (
  `clothing_id` int(11) NOT NULL AUTO_INCREMENT,
  `clothing_name` varchar(45) DEFAULT NULL,
  `quantity` varchar(45) DEFAULT NULL,
  `clothing_color` varchar(45) DEFAULT NULL,
  `clothing_size_id` varchar(11) DEFAULT NULL,
  `condition` enum('GOOD','BAD') DEFAULT 'GOOD',
  PRIMARY KEY (`clothing_id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clothing`
--

LOCK TABLES `clothing` WRITE;
/*!40000 ALTER TABLE `clothing` DISABLE KEYS */;
INSERT INTO `clothing` VALUES (1,'Barong','2','Black','S','GOOD'),(2,'Barong','5','Black','M','GOOD'),(3,'Barong','3','Black','L','GOOD'),(4,'Barong','3','Black','XL','GOOD'),(5,'Barong','3','Cream','S','GOOD'),(6,'Infinity Dress','4','Shiny Royal Blue',NULL,'GOOD'),(7,'Infinity Dress','6','Matte Royal Blue',NULL,'GOOD'),(8,'Infinity Dress','6','Light Blue',NULL,'GOOD'),(9,'Scarf','15','Red',NULL,'GOOD'),(10,'Scarf','9','Green',NULL,'GOOD'),(11,'Skirt','10','Gold',NULL,'GOOD'),(12,'Alampay','8',NULL,NULL,'GOOD'),(13,'Shawl','10',NULL,NULL,'GOOD'),(14,'Filipiniana Detachable','8',NULL,NULL,'GOOD');
/*!40000 ALTER TABLE `clothing` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `clothing_size`
--

DROP TABLE IF EXISTS `clothing_size`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `clothing_size` (
  `size_id` int(11) NOT NULL,
  `clothing_size` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`size_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clothing_size`
--

LOCK TABLES `clothing_size` WRITE;
/*!40000 ALTER TABLE `clothing_size` DISABLE KEYS */;
INSERT INTO `clothing_size` VALUES (0,'S'),(1,'M'),(2,'L'),(3,'XL'),(4,'2XL');
/*!40000 ALTER TABLE `clothing_size` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `history`
--

DROP TABLE IF EXISTS `history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `history` (
  `history_id` int(11) NOT NULL AUTO_INCREMENT,
  `type` enum('BORROW','REPORT') NOT NULL,
  `borrowed_by` varchar(100) NOT NULL,
  `date` date NOT NULL,
  `category` varchar(50) NOT NULL,
  `item_name` varchar(100) NOT NULL,
  `quantity` int(11) NOT NULL,
  `sn` varchar(50) DEFAULT NULL,
  `status` varchar(50) NOT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`history_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `history`
--

LOCK TABLES `history` WRITE;
/*!40000 ALTER TABLE `history` DISABLE KEYS */;
/*!40000 ALTER TABLE `history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `instruments`
--

DROP TABLE IF EXISTS `instruments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `instruments` (
  `instru_id` int(11) NOT NULL AUTO_INCREMENT,
  `instrument_name` varchar(75) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `condition` enum('BAD','GOOD') DEFAULT 'GOOD',
  `img_dir` longblob DEFAULT NULL,
  PRIMARY KEY (`instru_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `instruments`
--

LOCK TABLES `instruments` WRITE;
/*!40000 ALTER TABLE `instruments` DISABLE KEYS */;
INSERT INTO `instruments` VALUES (1,'Korg KROSS2-88 Keyboard',1,'GOOD',NULL),(3,'Korg KROSS2-88 Charger',1,'GOOD',NULL),(4,'Keyboard Stand',1,'GOOD',NULL),(5,'Keyboard Case',1,'GOOD',NULL),(6,'Music Sheet Stand',1,'GOOD',NULL),(7,'CREATIVE Speakers',1,'GOOD',NULL);
/*!40000 ALTER TABLE `instruments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `login`
--

DROP TABLE IF EXISTS `login`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `login` (
  `login_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`login_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `login`
--

LOCK TABLES `login` WRITE;
/*!40000 ALTER TABLE `login` DISABLE KEYS */;
INSERT INTO `login` VALUES (1,'admin','admin'),(2,'user','password');
/*!40000 ALTER TABLE `login` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `members`
--

DROP TABLE IF EXISTS `members`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `members` (
  `member_id` int(11) NOT NULL AUTO_INCREMENT,
  `members_name` varchar(60) DEFAULT NULL,
  `program` varchar(50) DEFAULT NULL,
  `position` varchar(45) DEFAULT NULL,
  `birthdate` varchar(50) DEFAULT NULL,
  `address` varchar(200) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`member_id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `members`
--

LOCK TABLES `members` WRITE;
/*!40000 ALTER TABLE `members` DISABLE KEYS */;
INSERT INTO `members` VALUES (2,'BACULINAO, REGINA MARIE L.','CE','Member','11/20/2003','145 CALAMANSI ST. TRAMO HEIGHTS PUROK 6, BRGY. SUCAT, MUNTINLUPA CITY',NULL),(3,'BARCELON, JOHN BRYAN B.','CPE','Member','2/23/2004','LIRIO #3 EXTENSION ST., BRGY. CAA, LAS PIÑAS CITY',NULL),(4,'CARPIZ, LEMUEL JAY I.','AR','Member','2006-05-17','BLK 1 LOT 15 GENESIS ST. TEOSEJO SUBDIVISION, BRGY. TUNASAN, MUNTINLUPA CITY','member_profiles/member_4_1745846904.jpg'),(5,'CONSTANTINO, MIKAELA COLEEN D.','CPE','Member','4/4/2005','#95 NATIONAL ROAD, BRGY. PUTATAN, MUNTINLUPA CITY',NULL),(6,'DAYPUYART, REUBEN B.','ECE','Trainee','2006-01-02','ST. CATHERINE COMPOUND, BRGY. PUTATAN, MUNTINLUPA CITY','member_profiles/member_6_1745847220.jpg'),(7,'DE CASTRO, JUSTIN ALLEN M.','AR','Member','2005-09-05','PHASE 1 BLOCK 10 LOT 29 SOUTHVILLE3','member_profiles/member_7_1745847256.png'),(8,'ENCARNACION, LADYMAE A.','CE','Member','10/8/2003','SOUTHVILLE 3A EXT, BRGY. SAN ANTONIO, SAN PEDRO  CITY, LAGUNA',NULL),(9,'FAJARDO, ENRICO CHARLES E.','CE','Member','2002-02-12','#25 151 LUBACON APARTMENT PUROK 2, BRGY. CUPANG, MUNTINLUPA CITY','member_profiles/member_9_1745847279.png'),(10,'GACUTAN, KIMMI VICTORIA P.','ECE','Trainee','2006-10-07','136 WALING-WALING ST. LODORA VILLAGE, BRGY. TUNASAN, MUNTINLUPA CITY','member_profiles/member_10_1745847298.png'),(11,'GALICA, CLARK KENT C.','CE','Member','7/10/2003','#3-G PUROK 1 ADIA ST., BRGY. BAGUMBAYAN, TAGUIG CITY',NULL),(12,'ILAGAN, ROGELIO I I.','CPE','Member','2003-02-02','B5 L26, VERONA ST., CAMELLA HOMES 2D, SOLDIERS HILLS VILLAGE, PUTATAN, MUNTINLUPA CITY','member_profiles/member_12_1745847318.png'),(13,'MANUEL, UKRAINE KIEV V.','IE','Trainee','2005-04-11','162 ESTEHONOR COMPOUND WAWA STREET, BRGY. ALABANG, MUNTINLUPA CITY','member_profiles/member_13_1745847337.png'),(14,'NOBLEZA, JOHN MICHAEL C.','ECE','Member','2004-06-14','15 CAMIA ST L & B COMPOUND 1, BRGY. ALABANG MUNTINLUPA CITY','member_profiles/member_14_1745847364.png'),(15,'PACINOS, JOHN RINNEL L.','ECE','Member','2002-09-13','295 MONTILLANO ST., BRGY. ALABANG, MUNTINLUPA CITY','member_profiles/member_15_1745847420.png'),(16,'QUIZON, JILL ERIKA D.','AR','Member','2005-02-16','LOT 31 ACACIA COMPOUND ORCHIDS ST. SGHV, BRGY. PUTATAN MUNTINLUPA','member_profiles/member_16_1745847449.png'),(17,'RAMIREZ, KIM D.','ECE','Trainee','2005-05-18','218 VIÑALON ST. BRGY. CUPANG, MUNTINLUPA CITY','member_profiles/member_17_1745847470.png'),(18,'RED, CLIFFORD KEN A.','CE','Trainee','2003-12-01','BLK 4 LOT 11 BRGY. MAGSAYSAY, SAN PEDRO CITY, LAGUNA','member_profiles/member_18_1745847500.png'),(19,'RIVERA, RONNIE JR. S.','CE','Member','2004-08-31','782 BERGANTIÑOS COMPOUND, BRGY. CUYAB, SAN PEDRO CITY, LAGUNA.','member_profiles/member_19_1745847525.png'),(20,'ROSAURO, BENEDICT M.','ME','Member','2005-06-25','PH. 3, BLOCK 49, LOT 16, SOUTHVILLE 3, POBLACION, MUNT. CITY','member_profiles/member_20_1745847541.png'),(21,'TOLEDO, PRECIOUS MAE G.','CE','Trainee','11/1/2005','BRGY. BULI, MUNTINLUPA CITY',NULL),(22,'TORRELIZA, JANRHEY G.','CE','Member','2005-01-02','BLK 31 LOT 17 LINDEN ST. BRGY. LANGGAM, SAN PEDRO, LAGUNA.','member_profiles/member_22_1745847560.png'),(24,'ALDAYA, ALEXANDRA LIEN S.','AR','Trainee','2006-12-29','BLK 78 LOT 4 A MABINI ST., BRGY. LARAM, SAN PEDRO CITY','member_profiles/member_1745846421_7615.jpg');
/*!40000 ALTER TABLE `members` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_login`
--

DROP TABLE IF EXISTS `user_login`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_login` (
  `id_user_login` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id_user_login`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_login`
--

LOCK TABLES `user_login` WRITE;
/*!40000 ALTER TABLE `user_login` DISABLE KEYS */;
INSERT INTO `user_login` VALUES (1,'user','user');
/*!40000 ALTER TABLE `user_login` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-04-28 21:48:00
