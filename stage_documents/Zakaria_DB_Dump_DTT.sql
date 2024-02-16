-- MySQL dump 10.13  Distrib 8.0.33, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: dtt_assessment
-- ------------------------------------------------------
-- Server version	5.5.5-10.4.32-MariaDB

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
-- Table structure for table `employee`
--

DROP TABLE IF EXISTS `employee`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `employee` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(45) NOT NULL,
  `last_name` varchar(45) NOT NULL,
  `email` varchar(45) NOT NULL,
  `phone_number` varchar(45) NOT NULL,
  `position` varchar(45) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email_UNIQUE` (`email`),
  UNIQUE KEY `phone_number_UNIQUE` (`phone_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee`
--

LOCK TABLES `employee` WRITE;
/*!40000 ALTER TABLE `employee` DISABLE KEYS */;
/*!40000 ALTER TABLE `employee` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `facility`
--

DROP TABLE IF EXISTS `facility`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `facility` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `creation_date` date NOT NULL DEFAULT current_timestamp(),
  `location_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_Facility_Location_idx` (`location_id`),
  CONSTRAINT `fk_Facility_Location` FOREIGN KEY (`location_id`) REFERENCES `location` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=79 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `facility`
--

LOCK TABLES `facility` WRITE;
/*!40000 ALTER TABLE `facility` DISABLE KEYS */;
INSERT INTO `facility` VALUES (72,'The Companys Ship','2018-05-29',2),(73,'The Companys Ship','2018-05-29',2),(74,'The Company\'s Ship','2018-12-29',1),(75,'The Company recruiter','2024-02-16',3),(76,'The Company recruiter','2024-02-16',3),(78,'The Company recruiter','2013-02-23',3);
/*!40000 ALTER TABLE `facility` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `facility_has_employee`
--

DROP TABLE IF EXISTS `facility_has_employee`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `facility_has_employee` (
  `facility_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  PRIMARY KEY (`facility_id`,`employee_id`),
  KEY `fk_facility_has_employee_employee1_idx` (`employee_id`),
  KEY `fk_facility_has_employee_facility1_idx` (`facility_id`),
  CONSTRAINT `fk_facility_has_employee_employee1` FOREIGN KEY (`employee_id`) REFERENCES `employee` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_facility_has_employee_facility1` FOREIGN KEY (`facility_id`) REFERENCES `facility` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `facility_has_employee`
--

LOCK TABLES `facility_has_employee` WRITE;
/*!40000 ALTER TABLE `facility_has_employee` DISABLE KEYS */;
/*!40000 ALTER TABLE `facility_has_employee` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `facility_has_tag`
--

DROP TABLE IF EXISTS `facility_has_tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `facility_has_tag` (
  `facility_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL,
  PRIMARY KEY (`facility_id`,`tag_id`),
  KEY `fk_Facility_has_Tag_Tag1_idx` (`tag_id`),
  KEY `fk_Facility_has_Tag_Facility1_idx` (`facility_id`),
  CONSTRAINT `fk_Facility_has_Tag_Facility1` FOREIGN KEY (`facility_id`) REFERENCES `facility` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_Facility_has_Tag_Tag1` FOREIGN KEY (`tag_id`) REFERENCES `tag` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `facility_has_tag`
--

LOCK TABLES `facility_has_tag` WRITE;
/*!40000 ALTER TABLE `facility_has_tag` DISABLE KEYS */;
INSERT INTO `facility_has_tag` VALUES (72,13),(72,14),(72,15),(73,13),(73,14),(73,15),(74,18),(74,19),(74,21),(75,13),(75,17),(76,13),(76,17),(78,13),(78,17);
/*!40000 ALTER TABLE `facility_has_tag` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `location`
--

DROP TABLE IF EXISTS `location`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `location` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `city` varchar(45) NOT NULL,
  `zip_code` varchar(7) NOT NULL,
  `country_code` varchar(5) NOT NULL,
  `phone_number` varchar(15) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `location`
--

LOCK TABLES `location` WRITE;
/*!40000 ALTER TABLE `location` DISABLE KEYS */;
INSERT INTO `location` VALUES (1,'Amsterdam','2238 FC','+31','0612345678'),(2,'Amstelveen','1183 NK','+31','0643562341'),(3,'Utrecht','4409 GF','+31','0677743123');
/*!40000 ALTER TABLE `location` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tag`
--

DROP TABLE IF EXISTS `tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_UNIQUE` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tag`
--

LOCK TABLES `tag` WRITE;
/*!40000 ALTER TABLE `tag` DISABLE KEYS */;
INSERT INTO `tag` VALUES (18,'Hello'),(17,'Jobs'),(13,'Lethal'),(14,'Moons'),(21,'Once'),(20,'Once More'),(16,'Sell'),(15,'Space'),(19,'World');
/*!40000 ALTER TABLE `tag` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-02-16  2:17:45
