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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
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
  `clothing_size_id` int(11) DEFAULT NULL,
  `condition` enum('GOOD','BAD') DEFAULT 'GOOD',
  PRIMARY KEY (`clothing_id`),
  KEY `clothing_size_FK_idx` (`clothing_size_id`),
  CONSTRAINT `clothing_size_FK` FOREIGN KEY (`clothing_size_id`) REFERENCES `clothing_size` (`size_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clothing`
--

LOCK TABLES `clothing` WRITE;
/*!40000 ALTER TABLE `clothing` DISABLE KEYS */;
INSERT INTO `clothing` VALUES (1,'Barong','2','Black',0,'GOOD'),(2,'Barong','5','Black',1,'GOOD'),(3,'Barong','3','Black',2,'GOOD'),(4,'Barong','3','Black',3,'GOOD'),(5,'Barong','3','Cream',0,'GOOD'),(6,'Infinity Dress','4','Shiny Royal Blue',NULL,'GOOD'),(7,'Infinity Dress','6','Matte Royal Blue',NULL,'GOOD'),(8,'Infinity Dress','6','Light Blue',NULL,'GOOD'),(9,'Scarf','15','Red',NULL,'GOOD'),(10,'Scarf','9','Green',NULL,'GOOD'),(11,'Skirt','10','Gold',NULL,'GOOD'),(12,'Alampay','8',NULL,NULL,'GOOD'),(13,'Shawl','10',NULL,NULL,'GOOD'),(14,'Filipiniana Detachable','8',NULL,NULL,'GOOD');
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
  PRIMARY KEY (`instru_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `instruments`
--

LOCK TABLES `instruments` WRITE;
/*!40000 ALTER TABLE `instruments` DISABLE KEYS */;
INSERT INTO `instruments` VALUES (1,'Korg KROSS2-88 Keyboard',1,'GOOD'),(3,'Korg KROSS2-88 Charger',1,'GOOD'),(4,'Keyboard Stand',1,'GOOD'),(5,'Keyboard Case',1,'GOOD'),(6,'Music Sheet Stand',1,'GOOD'),(7,'CREATIVE Speakers',1,'GOOD');
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `login`
--

LOCK TABLES `login` WRITE;
/*!40000 ALTER TABLE `login` DISABLE KEYS */;
INSERT INTO `login` VALUES (1,'admin','admin');
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
  `year` int(11) DEFAULT NULL,
  `program` varchar(50) DEFAULT NULL,
  `position` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`member_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `members`
--

LOCK TABLES `members` WRITE;
/*!40000 ALTER TABLE `members` DISABLE KEYS */;
INSERT INTO `members` VALUES (1,'Hans Sese',2,'CPE','Admin'),(2,'Vince Luces',2,'AC','Admin'),(3,'Gian Mustar',3,'ME','Member'),(4,'Gavrel Rodriguez',4,'ECE','Member');
/*!40000 ALTER TABLE `members` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `history`
--

DROP TABLE IF EXISTS `history`;
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
  `remarks` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`history_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-04-19 13:35:45
