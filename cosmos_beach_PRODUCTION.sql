-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: cosmos_beach
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

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
-- Table structure for table `activities`
--

DROP TABLE IF EXISTS `activities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `activities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `prix` decimal(10,2) NOT NULL,
  `duree` varchar(50) DEFAULT NULL,
  `capacite_max` int(11) DEFAULT NULL,
  `type` enum('piscine_vip','piscine_ordinaire','restaurant','chambre','zoo','jeux','autre') NOT NULL DEFAULT 'autre',
  `image_url` varchar(500) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_type` (`type`),
  KEY `idx_is_active` (`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activities`
--

LOCK TABLES `activities` WRITE;
/*!40000 ALTER TABLE `activities` DISABLE KEYS */;
INSERT INTO `activities` VALUES (1,'Piscine VIP','Accès illimité à la piscine VIP avec transat et service boisson. Idéal pour se détendre au calme.',15000.00,'Journée',50,'piscine_vip','img/piscine-VIP.jpg',1,'2026-05-06 05:40:45'),(2,'Restaurant Gastronomique','Réservation d\'une table pour deux personnes avec menu dégustation local.',30000.00,'3 Heures',100,'restaurant','img/Nourriture.jpg',1,'2026-05-06 05:40:45'),(3,'Visite du Mini Zoo','Découvrez notre collection d\'animaux exotiques et locaux, parfait pour les enfants.',5000.00,'2 Heures',200,'zoo','img/activite-jeu.jpg',1,'2026-05-06 05:40:45'),(4,'Chambre Deluxe','Nuitée dans notre suite Deluxe avec vue sur l\'océan et petit-déjeuner inclus.',80000.00,'Nuit',2,'chambre','img/Chambre-hotel.jpg',1,'2026-05-06 05:40:45'),(5,'Piscine Ordinaire','Accès à la grande piscine publique avec toboggans pour toute la famille.',5000.00,'Journée',300,'piscine_ordinaire','img/piscine publique.jpg',1,'2026-05-06 05:40:45'),(6,'Jeux pour enfants','Laissez vos enfants s\'épanuir avec nos différents jeux',21000.00,'Journée',20,'jeux','/Application-Defense/img/1778149469_activite-jeu.jpg',1,'2026-05-07 10:24:29');
/*!40000 ALTER TABLE `activities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `audit_logs`
--

DROP TABLE IF EXISTS `audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `audit_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `user_name` varchar(255) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `entity_type` varchar(50) DEFAULT NULL,
  `entity_id` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(500) DEFAULT NULL,
  `status` enum('success','failed') NOT NULL DEFAULT 'success',
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_action` (`action`),
  KEY `idx_timestamp` (`timestamp`),
  KEY `idx_entity` (`entity_type`,`entity_id`),
  CONSTRAINT `fk_al_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=84 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_logs`
--

LOCK TABLES `audit_logs` WRITE;
/*!40000 ALTER TABLE `audit_logs` DISABLE KEYS */;
INSERT INTO `audit_logs` VALUES (1,NULL,' ','login_failed','auth',NULL,'Tentative de connexion échouée: Mot de passe incorrect','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0','failed','2026-05-06 05:42:20'),(2,NULL,' ','login_failed','auth',NULL,'Tentative de connexion échouée: Mot de passe incorrect','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0','failed','2026-05-06 05:43:55'),(3,NULL,' ','login_failed','auth',NULL,'Tentative de connexion échouée: Mot de passe incorrect','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0','failed','2026-05-06 05:44:14'),(4,2,'Sophonie Douceur ','login','auth',2,'Connexion réussie','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0','success','2026-05-06 05:45:39'),(5,2,'Sophonie Douceur ','logout','auth',2,'Déconnexion','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0','success','2026-05-06 05:46:56'),(6,2,'Sophonie Douceur ','login','auth',2,'Connexion réussie','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0','success','2026-05-06 05:47:23'),(7,NULL,' ','login_failed','auth',NULL,'Tentative de connexion échouée: Mot de passe incorrect','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0','failed','2026-05-06 08:40:30'),(8,2,'Sophonie Douceur ','login','auth',2,'Connexion réussie','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0','success','2026-05-06 08:41:28'),(9,2,'Sophonie Douceur ','logout','auth',2,'Déconnexion','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0','success','2026-05-06 09:00:59'),(10,2,'Sophonie Douceur ','login','auth',2,'Connexion réussie','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0','success','2026-05-06 09:17:58'),(11,2,'Sophonie Douceur ','logout','auth',2,'Déconnexion','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0','success','2026-05-06 09:18:39'),(12,NULL,' ','login_failed','auth',NULL,'Échec connexion : sophoniedouceur@gmail.com','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0','failed','2026-05-06 10:20:03'),(13,2,'Sophonie Douceur ','login','auth',2,'Connexion réussie','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0','success','2026-05-06 10:20:23'),(14,2,'Sophonie Douceur ','login','auth',2,'Connexion réussie','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0','success','2026-05-06 10:47:41'),(15,2,'Sophonie Douceur ','logout','auth',2,'Déconnexion','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0','success','2026-05-06 11:19:59'),(16,NULL,' ','register','auth',NULL,'Nouvel utilisateur : noelykanga@gmail.com','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0','success','2026-05-06 11:51:34'),(17,3,'Noely Kanga ','login','auth',3,'Connexion réussie','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0','success','2026-05-06 11:52:55'),(18,2,'Sophonie Douceur ','login','auth',2,'Connexion réussie','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','success','2026-05-06 11:54:07'),(19,3,'Noely Kanga ','logout','auth',3,'Déconnexion','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0','success','2026-05-06 12:50:26'),(20,3,'Noely Kanga ','login','auth',3,'Connexion réussie','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0','success','2026-05-06 12:51:19'),(21,3,'Noely Kanga ','logout','auth',3,'Déconnexion','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0','success','2026-05-06 12:51:40'),(22,2,'Sophonie Douceur ','login','auth',2,'Connexion réussie','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','success','2026-05-06 13:39:53'),(23,2,'Sophonie Douceur ','login','auth',2,'Connexion réussie','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','success','2026-05-06 15:05:02'),(24,2,'Sophonie Douceur ','logout','auth',2,'Déconnexion','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','success','2026-05-06 15:08:10'),(25,2,'Sophonie Douceur ','login','auth',2,'Connexion réussie','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','success','2026-05-06 15:08:21'),(26,2,'Sophonie Douceur ','view_user_permissions','permissions',NULL,'Consultation gestion permissions utilisateurs','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','success','2026-05-06 15:08:28'),(27,2,'Sophonie Douceur ','logout','auth',2,'Déconnexion','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','success','2026-05-06 15:10:06'),(28,NULL,' ','register','auth',NULL,'Nouvel utilisateur : tahilormoyikoua@gmail.com','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','success','2026-05-06 15:15:26'),(29,NULL,' ','login_failed','auth',NULL,'Échec connexion : tahilormoyikoua@gmail.com','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','failed','2026-05-06 15:18:19'),(30,NULL,' ','login_failed','auth',NULL,'Échec connexion : tahilormoyikoua@gmail.com','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','failed','2026-05-06 15:18:47'),(31,NULL,' ','login_failed','auth',NULL,'Échec connexion : tahilormoyikoua@gmail.com','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','failed','2026-05-06 15:19:21'),(32,NULL,' ','register','auth',NULL,'Nouvel utilisateur : jaelanlandu@gmail.com','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','success','2026-05-06 20:00:51'),(33,5,'Jaela Nlandu ','login','auth',5,'Connexion réussie','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','success','2026-05-06 20:30:49'),(34,5,'Jaela Nlandu ','logout','auth',5,'Déconnexion','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','success','2026-05-06 20:32:31'),(35,5,'Jaela Nlandu ','login','auth',5,'Connexion réussie','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','success','2026-05-06 20:32:49'),(36,5,'Jaela Nlandu ','login','auth',5,'Connexion réussie','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','success','2026-05-07 07:33:58'),(37,5,'Jaela Nlandu ','logout','auth',5,'Déconnexion','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','success','2026-05-07 07:35:13'),(38,5,'Jaela Nlandu ','login','auth',5,'Connexion réussie','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','success','2026-05-07 07:48:28'),(39,2,'Sophonie Douceur ','login','auth',2,'Connexion réussie','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','success','2026-05-07 07:49:55'),(40,2,'Sophonie Douceur ','view_user_permissions','permissions',NULL,'Consultation gestion permissions utilisateurs','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','success','2026-05-07 07:50:02'),(41,3,'Noely Kanga ','login','auth',3,'Connexion réussie','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0','success','2026-05-07 07:51:09'),(42,3,'Noely Kanga ','logout','auth',3,'Déconnexion','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0','success','2026-05-07 07:51:53'),(43,3,'Noely Kanga ','login','auth',3,'Connexion réussie','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0','success','2026-05-07 07:51:58'),(44,2,'Sophonie Douceur ','view_user_permissions','permissions',NULL,'Consultation gestion permissions utilisateurs','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','success','2026-05-07 07:52:10'),(45,2,'Sophonie Douceur ','view_user_permissions','permissions',NULL,'Consultation gestion permissions utilisateurs','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','success','2026-05-07 07:54:17'),(46,2,'Sophonie Douceur ','view_user_permissions','permissions',NULL,'Consultation gestion permissions utilisateurs','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','success','2026-05-07 07:56:23'),(47,2,'Sophonie Douceur ','logout','auth',2,'Déconnexion','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','success','2026-05-07 08:05:36'),(48,NULL,' ','password_reset','auth',4,'Mot de passe réinitialisé','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','success','2026-05-07 08:13:13'),(49,NULL,' ','password_reset','auth',4,'Mot de passe réinitialisé','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','success','2026-05-07 08:15:00'),(50,4,'heslin ','login','auth',4,'Connexion réussie','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','success','2026-05-07 08:15:40'),(51,4,'heslin ','logout','auth',4,'Déconnexion','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','success','2026-05-07 08:18:01'),(52,5,'Jaela Nlandu ','logout','auth',5,'Déconnexion','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','success','2026-05-07 08:22:00'),(53,2,'Sophonie Douceur ','login','auth',2,'Connexion réussie','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','success','2026-05-07 08:22:15'),(54,2,'Sophonie Douceur ','view_user_permissions','permissions',NULL,'Consultation gestion permissions utilisateurs','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','success','2026-05-07 08:22:18'),(55,2,'Sophonie Douceur ','view_user_permissions','permissions',NULL,'Consultation gestion permissions utilisateurs','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','success','2026-05-07 08:22:37'),(56,2,'Sophonie Douceur ','view_user_permissions','permissions',NULL,'Consultation gestion permissions utilisateurs','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','success','2026-05-07 08:22:53'),(57,2,'Sophonie Douceur ','view_user_permissions','permissions',NULL,'Consultation gestion permissions utilisateurs','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','success','2026-05-07 09:16:57'),(58,2,'Sophonie Douceur ','update_user_permissions','permissions',3,'Droits utilisateur mis à jour','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','success','2026-05-07 09:18:11'),(59,2,'Sophonie Douceur ','view_user_permissions','permissions',NULL,'Consultation gestion permissions utilisateurs','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','success','2026-05-07 09:18:12'),(60,3,'Noely Kanga ','login','auth',3,'Connexion réussie','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0','success','2026-05-07 09:32:19'),(61,2,'Sophonie Douceur ','logout','auth',2,'Déconnexion','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','success','2026-05-07 09:57:29'),(62,3,'Noely Kanga ','logout','auth',3,'Déconnexion','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0','success','2026-05-07 10:19:56'),(63,2,'Sophonie Douceur ','login','auth',2,'Connexion réussie','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0','success','2026-05-07 10:20:17'),(64,2,'Sophonie Douceur ','logout','auth',2,'Déconnexion','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0','success','2026-05-07 10:25:40'),(65,NULL,' ','login_failed','auth',NULL,'Échec connexion : tahilormoyikoua@gmail.com','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0','failed','2026-05-07 10:26:35'),(66,NULL,' ','register','auth',NULL,'Nouvel utilisateur : lubongodouceur@gmail.com','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0','success','2026-05-07 10:27:20'),(67,6,'NGOYI ANICET ','login','auth',6,'Connexion réussie','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0','success','2026-05-07 10:27:40'),(68,6,'NGOYI ANICET ','logout','auth',6,'Déconnexion','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0','success','2026-05-07 10:30:22'),(69,2,'Sophonie Douceur ','login','auth',2,'Connexion réussie','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','success','2026-05-07 10:31:04'),(70,3,'Noely Kanga ','login','auth',3,'Connexion réussie','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0','success','2026-05-07 10:33:05'),(71,6,'NGOYI ANICET ','login','auth',6,'Connexion réussie','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','success','2026-05-07 10:34:12'),(72,3,'Noely Kanga ','logout','auth',3,'Déconnexion','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0','success','2026-05-07 11:01:27'),(73,NULL,' ','login_failed','auth',NULL,'Échec connexion : marketeur@cosmosbeach.com','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0','failed','2026-05-07 11:01:55'),(74,2,'Sophonie Douceur ','logout','auth',2,'Déconnexion','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','success','2026-05-07 11:02:43'),(75,5,'Jaela Nlandu ','login','auth',5,'Connexion réussie','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','success','2026-05-07 11:02:59'),(76,5,'Jaela Nlandu ','create_event','events',NULL,'Événement créé : Concert de Fally IPUPA','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','success','2026-05-07 11:07:04'),(77,NULL,' ','login_failed','auth',NULL,'Échec connexion : tahilormoyikoua@gmail.com','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0','failed','2026-05-07 11:08:36'),(78,NULL,' ','login_failed','auth',NULL,'Échec connexion : tahilormoyikoua@gmail.com','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0','failed','2026-05-07 11:09:10'),(79,NULL,' ','login_failed','auth',NULL,'Échec connexion : tahilormoyikoua@gmail.com','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0','failed','2026-05-07 11:09:42'),(80,NULL,' ','password_reset','auth',4,'Mot de passe réinitialisé','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0','success','2026-05-07 11:14:37'),(81,4,'heslin ','login','auth',4,'Connexion réussie','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0','success','2026-05-07 11:15:15'),(82,4,'heslin ','book_ticket','events',1,'Ticket TKT-00001 réservé pour l\'événement ID 1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0','success','2026-05-07 11:15:37'),(83,5,'Jaela Nlandu ','create_event','events',NULL,'Événement créé : Concert de Fally IPUPA','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','success','2026-05-07 11:26:28');
/*!40000 ALTER TABLE `audit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `event_tickets`
--

DROP TABLE IF EXISTS `event_tickets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `event_tickets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `numero_ticket` varchar(20) NOT NULL COMMENT 'Ex: TKT-00001',
  `nombre_places` int(11) NOT NULL DEFAULT 1,
  `montant_total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `statut` enum('EN_ATTENTE','CONFIRME','ANNULE') NOT NULL DEFAULT 'EN_ATTENTE',
  `email_envoye` tinyint(1) NOT NULL DEFAULT 0,
  `date_achat` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_numero_ticket` (`numero_ticket`),
  KEY `idx_event_id` (`event_id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_statut` (`statut`),
  CONSTRAINT `fk_et_event` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_et_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `event_tickets`
--

LOCK TABLES `event_tickets` WRITE;
/*!40000 ALTER TABLE `event_tickets` DISABLE KEYS */;
INSERT INTO `event_tickets` VALUES (1,1,4,'TKT-00001',2,4000.00,'CONFIRME',1,'2026-05-07 11:15:34');
/*!40000 ALTER TABLE `event_tickets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `events`
--

DROP TABLE IF EXISTS `events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titre` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `date_debut` datetime NOT NULL,
  `date_fin` datetime NOT NULL,
  `image_url` varchar(500) DEFAULT NULL,
  `prix_ticket` decimal(10,2) NOT NULL DEFAULT 0.00,
  `capacite_max` int(11) NOT NULL DEFAULT 100,
  `lieu` varchar(255) DEFAULT NULL,
  `type_event` enum('concert','soiree','sport','promotion','autre') NOT NULL DEFAULT 'autre',
  `likes_count` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` int(11) DEFAULT NULL,
  `date_creation` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_date_debut` (`date_debut`),
  KEY `idx_is_active` (`is_active`),
  KEY `fk_event_creator` (`created_by`),
  CONSTRAINT `fk_event_creator` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `events`
--

LOCK TABLES `events` WRITE;
/*!40000 ALTER TABLE `events` DISABLE KEYS */;
INSERT INTO `events` VALUES (1,'Concert de Fally IPUPA','Meilleur Concert de l\'année','2026-05-17 12:00:00','2026-05-17 22:00:00','https://www.facebook.com/share/p/1BLcdUQsXH/',2000.00,100,'Cosmos Beach - Piscine Ordinaire','concert',0,1,5,'2026-05-07 11:07:04'),(2,'Concert de Fally IPUPA','Venez nombreux c\'est le meilleur concert de l\'année !!','2026-05-29 12:00:00','2026-05-29 23:00:00','img/events/event_1778153188_fff0f50f.jpg',15000.00,300,'Cosmos Beach - Piscine VIP','concert',0,1,5,'2026-05-07 11:26:28');
/*!40000 ALTER TABLE `events` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `login_history`
--

DROP TABLE IF EXISTS `login_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `login_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `email` varchar(150) NOT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `login_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `logout_time` timestamp NULL DEFAULT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` varchar(500) DEFAULT NULL,
  `browser` varchar(100) DEFAULT NULL,
  `os` varchar(100) DEFAULT NULL,
  `device_type` varchar(50) DEFAULT NULL,
  `status` enum('success','failed') NOT NULL DEFAULT 'success',
  `failure_reason` varchar(255) DEFAULT NULL,
  `is_suspicious` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_email` (`email`),
  KEY `idx_login_time` (`login_time`),
  KEY `idx_ip` (`ip_address`),
  KEY `idx_suspicious` (`is_suspicious`),
  CONSTRAINT `fk_lh_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `login_history`
--

LOCK TABLES `login_history` WRITE;
/*!40000 ALTER TABLE `login_history` DISABLE KEYS */;
INSERT INTO `login_history` VALUES (1,NULL,'admin@cosmosbeach.com','','','2026-05-06 05:42:20',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0','Chrome','Windows','Desktop','failed','Mot de passe incorrect',0),(2,NULL,'admin@cosmosbeach.com','','','2026-05-06 05:43:55',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0','Chrome','Windows','Desktop','failed','Mot de passe incorrect',0),(3,NULL,'admin@cosmosbeach.com','','','2026-05-06 05:44:14',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0','Chrome','Windows','Desktop','failed','Mot de passe incorrect',0),(4,2,'sophoniedouceur@gmail.com','','Sophonie Douceur','2026-05-06 05:45:39','2026-05-06 05:46:56','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0','Chrome','Windows','Desktop','success',NULL,0),(5,2,'sophoniedouceur@gmail.com','','Sophonie Douceur','2026-05-06 05:47:23',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0','Chrome','Windows','Desktop','success',NULL,0),(6,NULL,'admin@cosmosbeach.com','','','2026-05-06 08:40:30',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0','Chrome','Windows','Desktop','failed','Mot de passe incorrect',0),(7,2,'sophoniedouceur@gmail.com','','Sophonie Douceur','2026-05-06 08:41:28','2026-05-06 09:00:59','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0','Chrome','Windows','Desktop','success',NULL,0),(8,2,'sophoniedouceur@gmail.com','','Sophonie Douceur','2026-05-06 09:17:58','2026-05-06 09:18:39','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0','Chrome','Windows','Desktop','success',NULL,0),(9,NULL,'sophoniedouceur@gmail.com','','','2026-05-06 10:20:02',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0','Chrome','Windows','Desktop','failed','Mot de passe incorrect',0),(10,2,'sophoniedouceur@gmail.com','','Sophonie Douceur','2026-05-06 10:20:23',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0','Chrome','Windows','Desktop','success',NULL,0),(11,2,'sophoniedouceur@gmail.com','','Sophonie Douceur','2026-05-06 10:47:41','2026-05-06 11:19:59','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0','Chrome','Windows','Desktop','success',NULL,0),(12,3,'noelykanga@gmail.com','','Noely Kanga','2026-05-06 11:52:55','2026-05-06 12:50:26','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0','Chrome','Windows','Desktop','success',NULL,0),(13,2,'sophoniedouceur@gmail.com','','Sophonie Douceur','2026-05-06 11:54:07',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','Chrome','Windows','Desktop','success',NULL,0),(14,3,'noelykanga@gmail.com','','Noely Kanga','2026-05-06 12:51:19','2026-05-06 12:51:40','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0','Chrome','Windows','Desktop','success',NULL,0),(15,2,'sophoniedouceur@gmail.com','','Sophonie Douceur','2026-05-06 13:39:53',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','Chrome','Windows','Desktop','success',NULL,0),(16,2,'sophoniedouceur@gmail.com','','Sophonie Douceur','2026-05-06 15:05:02','2026-05-06 15:08:10','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','Chrome','Windows','Desktop','success',NULL,0),(17,2,'sophoniedouceur@gmail.com','','Sophonie Douceur','2026-05-06 15:08:21','2026-05-06 15:10:06','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','Chrome','Windows','Desktop','success',NULL,0),(18,NULL,'tahilormoyikoua@gmail.com','','','2026-05-06 15:18:19',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','Chrome','Windows','Desktop','failed','Mot de passe incorrect',0),(19,NULL,'tahilormoyikoua@gmail.com','','','2026-05-06 15:18:47',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','Chrome','Windows','Desktop','failed','Mot de passe incorrect',0),(20,NULL,'tahilormoyikoua@gmail.com','','','2026-05-06 15:19:21',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','Chrome','Windows','Desktop','failed','Mot de passe incorrect',0),(21,5,'jaelanlandu@gmail.com','','Jaela Nlandu','2026-05-06 20:30:49','2026-05-06 20:32:31','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','Chrome','Windows','Desktop','success',NULL,0),(22,5,'jaelanlandu@gmail.com','','Jaela Nlandu','2026-05-06 20:32:49',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','Chrome','Windows','Desktop','success',NULL,0),(23,5,'jaelanlandu@gmail.com','','Jaela Nlandu','2026-05-07 07:33:58','2026-05-07 07:35:13','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','Chrome','Windows','Desktop','success',NULL,0),(24,5,'jaelanlandu@gmail.com','','Jaela Nlandu','2026-05-07 07:48:28','2026-05-07 08:22:00','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','Chrome','Windows','Desktop','success',NULL,0),(25,2,'sophoniedouceur@gmail.com','','Sophonie Douceur','2026-05-07 07:49:55','2026-05-07 08:05:36','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','Chrome','Windows','Desktop','success',NULL,0),(26,3,'noelykanga@gmail.com','','Noely Kanga','2026-05-07 07:51:09','2026-05-07 07:51:53','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0','Chrome','Windows','Desktop','success',NULL,0),(27,3,'noelykanga@gmail.com','','Noely Kanga','2026-05-07 07:51:58',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0','Chrome','Windows','Desktop','success',NULL,0),(28,4,'tahilormoyikoua@gmail.com','','heslin','2026-05-07 08:15:40','2026-05-07 08:18:01','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','Chrome','Windows','Desktop','success',NULL,0),(29,2,'sophoniedouceur@gmail.com','','Sophonie Douceur','2026-05-07 08:22:15','2026-05-07 09:57:29','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','Chrome','Windows','Desktop','success',NULL,0),(30,3,'noelykanga@gmail.com','','Noely Kanga','2026-05-07 09:32:19','2026-05-07 10:19:56','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0','Chrome','Windows','Desktop','success',NULL,0),(31,2,'sophoniedouceur@gmail.com','','Sophonie Douceur','2026-05-07 10:20:17','2026-05-07 10:25:40','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0','Chrome','Windows','Desktop','success',NULL,0),(32,NULL,'tahilormoyikoua@gmail.com','','','2026-05-07 10:26:35',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0','Chrome','Windows','Desktop','failed','Mot de passe incorrect',0),(33,6,'lubongodouceur@gmail.com','','NGOYI ANICET','2026-05-07 10:27:40','2026-05-07 10:30:22','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0','Chrome','Windows','Desktop','success',NULL,0),(34,2,'sophoniedouceur@gmail.com','','Sophonie Douceur','2026-05-07 10:31:04','2026-05-07 11:02:43','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','Chrome','Windows','Desktop','success',NULL,0),(35,3,'noelykanga@gmail.com','','Noely Kanga','2026-05-07 10:33:05','2026-05-07 11:01:27','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0','Chrome','Windows','Desktop','success',NULL,0),(36,6,'lubongodouceur@gmail.com','','NGOYI ANICET','2026-05-07 10:34:12',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','Chrome','Windows','Desktop','success',NULL,0),(37,NULL,'marketeur@cosmosbeach.com','','','2026-05-07 11:01:55',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0','Chrome','Windows','Desktop','failed','Mot de passe incorrect',0),(38,5,'jaelanlandu@gmail.com','','Jaela Nlandu','2026-05-07 11:02:59',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','Chrome','Windows','Desktop','success',NULL,0),(39,NULL,'tahilormoyikoua@gmail.com','','','2026-05-07 11:08:36',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0','Chrome','Windows','Desktop','failed','Mot de passe incorrect',0),(40,NULL,'tahilormoyikoua@gmail.com','','','2026-05-07 11:09:10',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0','Chrome','Windows','Desktop','failed','Mot de passe incorrect',0),(41,NULL,'tahilormoyikoua@gmail.com','','','2026-05-07 11:09:42',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0','Chrome','Windows','Desktop','failed','Mot de passe incorrect',0),(42,4,'tahilormoyikoua@gmail.com','','heslin','2026-05-07 11:15:15',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0','Chrome','Windows','Desktop','success',NULL,0);
/*!40000 ALTER TABLE `login_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `email` varchar(150) NOT NULL,
  `otp_hash` varchar(255) NOT NULL,
  `attempts` int(11) NOT NULL DEFAULT 0,
  `max_attempts` int(11) NOT NULL DEFAULT 5,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expires_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `verified_at` timestamp NULL DEFAULT NULL,
  `is_used` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_email` (`email`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_expires_at` (`expires_at`),
  KEY `idx_email_active` (`email`,`is_used`,`expires_at`),
  CONSTRAINT `fk_pr_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_resets`
--

LOCK TABLES `password_resets` WRITE;
/*!40000 ALTER TABLE `password_resets` DISABLE KEYS */;
INSERT INTO `password_resets` VALUES (1,1,'admin@cosmosbeach.com','$2y$10$KOChGaRMmHERKsyEyc.udeiFrcMuJnRbBVBwsNwq2FmewYHhJUwoO',0,5,'2026-05-06 11:46:30','2026-05-06 12:01:30',NULL,0),(2,2,'sophoniedouceur@gmail.com','$2y$10$B2BRaZq9.dechVsbeeAomuSUWA6DiHwhWDbImgL9FxhQopUqt9eeC',2,5,'2026-05-06 11:47:33','2026-05-06 12:02:33',NULL,1),(3,2,'sophoniedouceur@gmail.com','$2y$10$a3Zpnk.3AdyQWxvemde2KuBft8Zz2WxOeX2BIYAuADAlQ/YWg8y8S',1,5,'2026-05-06 15:10:15','2026-05-06 15:25:15',NULL,1),(4,4,'tahilormoyikoua@gmail.com','$2y$10$VZuSKenSRDD2wfeWs9Ukqen9iDvYap5n2leb9oSKvGsjqhI3mEZ9S',0,5,'2026-05-06 15:15:46','2026-05-06 15:30:46','2026-05-06 15:16:26',1),(5,2,'sophoniedouceur@gmail.com','$2y$10$ZfucomJvQIuE2.9AAQQ1puEBF7i70/V2fzlqxWy1cZNDI0U5O8NfW',0,5,'2026-05-07 07:37:46','2026-05-07 07:52:46','2026-05-07 07:38:30',0),(6,4,'tahilormoyikoua@gmail.com','$2y$10$hq.2JLsqlX974L2WKHeeFOzNf4Zin2qZ2r9R.Y7ZLw0dXyfVqpS/y',0,5,'2026-05-07 08:06:06','2026-05-07 08:21:06',NULL,1),(7,4,'tahilormoyikoua@gmail.com','$2y$10$0XQkRAUoWe1hCcOWJFxIBO1VUb3ClxDauTlxKvHsBTx79.5Vuh4gq',1,5,'2026-05-07 08:06:10','2026-05-07 08:21:10',NULL,1),(8,4,'tahilormoyikoua@gmail.com','$2y$10$h2CrP8dD3kxbaueqpXmYRu2vO0L9erhSSv1zuDDD18HVg7L0WFUMy',3,5,'2026-05-07 08:07:35','2026-05-07 08:22:34',NULL,1),(9,4,'tahilormoyikoua@gmail.com','$2y$10$5d.o1cZZZvW7WyJXcrgpY.gIE3MuZxg/mHRElLnUx4DPK5Ma..FJ.',0,5,'2026-05-07 08:08:50','2026-05-07 08:23:50','2026-05-07 08:09:25',1),(10,4,'tahilormoyikoua@gmail.com','$2y$10$19ZOgXMuyWFPGeBEu68MR.kZjmFKTDQQBLUtTEWbWo3rBACspv6f6',0,5,'2026-05-07 08:13:48','2026-05-07 08:28:48','2026-05-07 08:14:34',1),(11,4,'tahilormoyikoua@gmail.com','$2y$10$You5L/F6gDtdUk9E1OIIbeJ9vIktdgfpDGUtj4ZSNwWKmeQTpzAEm',2,5,'2026-05-07 11:10:01','2026-05-07 11:25:01',NULL,1),(12,4,'tahilormoyikoua@gmail.com','$2y$10$zLX8rHTb2UsN5Cl/eP8KLOh6FjWwlJO6go.e4ID3DBRJ63DnD.0za',5,5,'2026-05-07 11:11:00','2026-05-07 11:26:00',NULL,1),(13,4,'tahilormoyikoua@gmail.com','$2y$10$v8TXZBzRwlUkozoC/UnGUuYgxnE.2FQWb6iXoXiZPNWQqb9o3gikS',0,5,'2026-05-07 11:12:39','2026-05-07 11:27:39','2026-05-07 11:13:19',1);
/*!40000 ALTER TABLE `password_resets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reservation_id` int(11) NOT NULL,
  `caissier_id` int(11) DEFAULT NULL COMMENT 'NULL si paiement en ligne',
  `montant` decimal(10,2) NOT NULL,
  `methode` enum('ESPECES','CARTE','MOBILE_MONEY') NOT NULL,
  `reference` varchar(100) DEFAULT NULL COMMENT 'Référence transaction externe',
  `date_paiement` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_reservation_id` (`reservation_id`),
  KEY `idx_caissier_id` (`caissier_id`),
  KEY `idx_date_paiement` (`date_paiement`),
  CONSTRAINT `fk_pay_caissier` FOREIGN KEY (`caissier_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_pay_reservation` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
INSERT INTO `payments` VALUES (1,1,2,7500.00,'ESPECES',NULL,'2026-05-06 10:47:54'),(2,3,3,84000.00,'ESPECES',NULL,'2026-05-07 10:33:10');
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(50) NOT NULL DEFAULT 'general',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_name` (`name`),
  KEY `idx_category` (`category`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES (1,'view_admin_dashboard','Voir le tableau de bord admin','admin','2026-05-06 05:40:45'),(2,'manage_users','Gérer les utilisateurs','users','2026-05-06 05:40:45'),(3,'view_users','Voir la liste des utilisateurs','users','2026-05-06 05:40:45'),(4,'activate_deactivate_users','Activer/Désactiver les utilisateurs','users','2026-05-06 05:40:45'),(5,'view_audit_logs','Voir les journaux d\'audit','audit','2026-05-06 05:40:45'),(6,'view_login_history','Voir l\'historique des connexions','audit','2026-05-06 05:40:45'),(7,'manage_permissions','Gérer les permissions et rôles','permissions','2026-05-06 05:40:45'),(8,'manage_roles','Gérer les rôles','permissions','2026-05-06 05:40:45'),(9,'manage_activities','Gérer les activités','activities','2026-05-06 05:40:45'),(10,'export_data','Exporter les données','export','2026-05-06 05:40:45'),(11,'view_reports','Voir les rapports financiers','reports','2026-05-06 05:40:45'),(12,'manage_events','Gérer les événements marketing','marketing','2026-05-06 05:40:45'),(13,'manage_reclamations','Gérer les réclamations','support','2026-05-06 05:40:45'),(14,'process_payments','Encaisser les paiements','caisse','2026-05-06 05:40:45'),(15,'manage_system_settings','Gérer les paramètres du système','system','2026-05-06 05:40:45');
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reclamations`
--

DROP TABLE IF EXISTS `reclamations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reclamations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `sujet` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `statut` enum('NOUVELLE','EN_COURS','RESOLUE') NOT NULL DEFAULT 'NOUVELLE',
  `traite_par` int(11) DEFAULT NULL COMMENT 'ID agent qui traite',
  `date_creation` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_traitement` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_statut` (`statut`),
  KEY `fk_rec_traite` (`traite_par`),
  CONSTRAINT `fk_rec_traite` FOREIGN KEY (`traite_par`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_rec_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reclamations`
--

LOCK TABLES `reclamations` WRITE;
/*!40000 ALTER TABLE `reclamations` DISABLE KEYS */;
/*!40000 ALTER TABLE `reclamations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reservations`
--

DROP TABLE IF EXISTS `reservations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reservations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `activite_id` int(11) NOT NULL,
  `date_reservation` datetime NOT NULL,
  `statut` enum('ATTENTE','CONFIRMEE','ANNULEE','PAYEE') NOT NULL DEFAULT 'ATTENTE',
  `montant_total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `nombre_personnes` int(11) NOT NULL DEFAULT 1,
  `nombre_chambres` int(11) DEFAULT NULL,
  `mode_reservation` varchar(20) DEFAULT NULL COMMENT 'partage|separe pour chambres',
  `nombre_tables` int(11) DEFAULT NULL,
  `nombre_adultes` int(11) DEFAULT NULL,
  `nombre_enfants` int(11) DEFAULT NULL,
  `date_creation` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_activite_id` (`activite_id`),
  KEY `idx_statut` (`statut`),
  KEY `idx_date_reservation` (`date_reservation`),
  CONSTRAINT `fk_res_activite` FOREIGN KEY (`activite_id`) REFERENCES `activities` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_res_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reservations`
--

LOCK TABLES `reservations` WRITE;
/*!40000 ALTER TABLE `reservations` DISABLE KEYS */;
INSERT INTO `reservations` VALUES (1,2,5,'2026-05-22 12:20:00','PAYEE',7500.00,2,NULL,NULL,NULL,1,1,'2026-05-06 10:21:09'),(2,2,4,'2026-05-24 16:30:00','ATTENTE',160000.00,2,2,'separe',NULL,NULL,NULL,'2026-05-06 10:49:02'),(3,6,6,'2026-05-07 12:28:00','PAYEE',84000.00,4,NULL,NULL,NULL,NULL,NULL,'2026-05-07 10:29:01');
/*!40000 ALTER TABLE `reservations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_permissions`
--

DROP TABLE IF EXISTS `role_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role_permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role` varchar(50) NOT NULL,
  `permission_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_role_perm` (`role`,`permission_id`),
  KEY `idx_role` (`role`),
  KEY `fk_rp_perm` (`permission_id`),
  CONSTRAINT `fk_rp_perm` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_permissions`
--

LOCK TABLES `role_permissions` WRITE;
/*!40000 ALTER TABLE `role_permissions` DISABLE KEYS */;
INSERT INTO `role_permissions` VALUES (1,'SUPER_ADMIN',9,'2026-05-06 05:40:45'),(2,'SUPER_ADMIN',1,'2026-05-06 05:40:45'),(3,'SUPER_ADMIN',5,'2026-05-06 05:40:45'),(4,'SUPER_ADMIN',6,'2026-05-06 05:40:45'),(5,'SUPER_ADMIN',14,'2026-05-06 05:40:45'),(6,'SUPER_ADMIN',10,'2026-05-06 05:40:45'),(7,'SUPER_ADMIN',12,'2026-05-06 05:40:45'),(8,'SUPER_ADMIN',7,'2026-05-06 05:40:45'),(9,'SUPER_ADMIN',8,'2026-05-06 05:40:45'),(10,'SUPER_ADMIN',11,'2026-05-06 05:40:45'),(11,'SUPER_ADMIN',13,'2026-05-06 05:40:45'),(12,'SUPER_ADMIN',15,'2026-05-06 05:40:45'),(13,'SUPER_ADMIN',2,'2026-05-06 05:40:45'),(14,'SUPER_ADMIN',3,'2026-05-06 05:40:45'),(15,'SUPER_ADMIN',4,'2026-05-06 05:40:45'),(16,'DIRECTEUR',4,'2026-05-06 05:40:45'),(17,'DIRECTEUR',10,'2026-05-06 05:40:45'),(18,'DIRECTEUR',9,'2026-05-06 05:40:45'),(19,'DIRECTEUR',12,'2026-05-06 05:40:45'),(20,'DIRECTEUR',13,'2026-05-06 05:40:45'),(21,'DIRECTEUR',2,'2026-05-06 05:40:45'),(22,'DIRECTEUR',1,'2026-05-06 05:40:45'),(23,'DIRECTEUR',5,'2026-05-06 05:40:45'),(24,'DIRECTEUR',6,'2026-05-06 05:40:45'),(25,'DIRECTEUR',11,'2026-05-06 05:40:45'),(26,'DIRECTEUR',3,'2026-05-06 05:40:45'),(31,'CAISSIER',14,'2026-05-06 05:40:45'),(32,'CAISSIER',11,'2026-05-06 05:40:45'),(34,'AGENT',13,'2026-05-06 05:40:45'),(35,'AGENT',3,'2026-05-06 05:40:45'),(36,'MARKETEUR',12,'2026-05-07 10:57:27'),(37,'MARKETEUR',11,'2026-05-07 10:57:27');
/*!40000 ALTER TABLE `role_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_permissions`
--

DROP TABLE IF EXISTS `user_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  `granted_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_user_perm` (`user_id`,`permission_id`),
  KEY `fk_up_perm` (`permission_id`),
  CONSTRAINT `fk_up_perm` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_up_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_permissions`
--

LOCK TABLES `user_permissions` WRITE;
/*!40000 ALTER TABLE `user_permissions` DISABLE KEYS */;
INSERT INTO `user_permissions` VALUES (1,3,14,NULL,'2026-05-07 09:18:11'),(2,3,11,NULL,'2026-05-07 09:18:11');
/*!40000 ALTER TABLE `user_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL DEFAULT '',
  `email` varchar(150) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('VISITEUR','AGENT','CAISSIER','DIRECTEUR','SUPER_ADMIN','MARKETEUR') NOT NULL DEFAULT 'VISITEUR',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `disabled_at` timestamp NULL DEFAULT NULL,
  `disabled_reason` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_email` (`email`),
  KEY `idx_role` (`role`),
  KEY `idx_is_active` (`is_active`),
  KEY `idx_last_login` (`last_login`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Administrateur','Super','admin@cosmosbeach.com','$2y$12$eImiTXuWVxfM37uY4JANjQ==','SUPER_ADMIN',1,NULL,'2026-05-06 05:40:45','2026-05-06 05:40:45',NULL,NULL),(2,'Sophonie Douceur','','sophoniedouceur@gmail.com','$2y$10$t0Wewc7rnAwfXaopWF2yZeiCWq1ZVGXfppAFrdgT/P8VeVS98jfSK','DIRECTEUR',1,'2026-05-07 10:31:04','2026-05-06 05:45:17','2026-05-07 10:31:04',NULL,NULL),(3,'Noely Kanga','','noelykanga@gmail.com','$2y$12$//zTY8UGLuq4xBtd50CnlOiGAmmD9QswNVsqo4fscGRFHz8xPX7jG','CAISSIER',1,'2026-05-07 10:33:05','2026-05-06 11:51:34','2026-05-07 10:33:05',NULL,NULL),(4,'heslin','','tahilormoyikoua@gmail.com','$2y$12$mdN9/.c2EzcoWG0tZ9si1O1DeAifhCtbb01BURtO1gER336qkaPCe','VISITEUR',1,'2026-05-07 11:15:15','2026-05-06 15:15:26','2026-05-07 11:15:15',NULL,NULL),(5,'Jaela Nlandu','','jaelanlandu@gmail.com','$2y$12$n.k0S8KDs/D0g25oAuMYoOc8F9aQGai2UCkJaABYq9..TfzmRi0iW','MARKETEUR',1,'2026-05-07 11:02:59','2026-05-06 20:00:51','2026-05-07 11:02:59',NULL,NULL),(6,'NGOYI ANICET','','lubongodouceur@gmail.com','$2y$12$phGq399G2B3ky9CPzQ2nJeYqAbZYJ47T5LSTo9BzNyNVKuf7O2aWa','VISITEUR',1,'2026-05-07 10:34:12','2026-05-07 10:27:20','2026-05-07 10:34:12',NULL,NULL),(7,'Marketing','Service','marketeur@cosmosbeach.com','$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','MARKETEUR',1,NULL,'2026-05-07 10:57:27','2026-05-07 10:57:27',NULL,NULL);
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

-- Dump completed on 2026-05-07 16:35:08
