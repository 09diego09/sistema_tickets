-- MySQL dump 10.13  Distrib 8.0.44, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: sistema_tickets
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
-- Table structure for table `notas_tickets`
--

DROP TABLE IF EXISTS `notas_tickets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notas_tickets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ticket_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `nota` text NOT NULL,
  `fecha` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `ticket_id` (`ticket_id`),
  KEY `usuario_id` (`usuario_id`),
  CONSTRAINT `notas_tickets_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`),
  CONSTRAINT `notas_tickets_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notas_tickets`
--

LOCK TABLES `notas_tickets` WRITE;
/*!40000 ALTER TABLE `notas_tickets` DISABLE KEYS */;
INSERT INTO `notas_tickets` VALUES (1,14,2,'hola','2025-12-29 10:07:21'),(2,14,2,'esta es una nota de prueba','2025-12-29 10:07:43'),(3,15,7,'nota','2025-12-29 10:36:41'),(4,15,2,'hola qué tal, esta es una nota de prueba, se cambia a en proceso.','2025-12-29 10:45:22'),(5,16,2,'nota de prueba','2025-12-29 10:56:53'),(6,16,2,'nota de prueba','2025-12-29 10:56:55'),(7,16,2,'nota de prueba','2025-12-29 10:56:58'),(8,16,2,'nota de prueba','2025-12-29 10:57:05'),(9,16,2,'prueba','2025-12-29 11:02:07'),(10,16,2,'nota de prueba','2025-12-29 11:07:56'),(11,17,2,'dafda','2025-12-29 14:09:04'),(12,17,2,'d','2025-12-29 14:15:02'),(13,16,2,'dfghjkl,.','2025-12-29 14:17:59'),(14,16,2,'d','2025-12-29 14:36:54'),(15,20,2,'hola','2025-12-29 16:30:43'),(16,20,2,'comentario de prueba','2025-12-29 16:30:52'),(17,21,2,'usuario comenta que su equipo explotó, mandaremos a alguien a revisarlo.','2025-12-29 16:45:40'),(18,21,7,'equipo revisado, está muerto, se tiene que gestionar uno nuevo, se cierra ticket.','2025-12-29 16:46:13'),(19,22,7,'actualización.','2025-12-29 16:51:49'),(20,22,7,'actualización.','2025-12-29 16:53:06'),(21,23,7,'hola','2025-12-29 16:55:15'),(22,22,7,'actualización.','2025-12-29 16:59:16'),(23,22,7,'actualización.','2025-12-29 16:59:39'),(24,22,7,'actualización','2025-12-29 17:01:42'),(25,22,2,'prueba de notificación 123','2025-12-29 17:02:19'),(26,22,2,'prueba de notificación 123','2025-12-29 17:02:25'),(27,22,7,'prueba de notificación 2','2025-12-29 17:03:05'),(28,24,2,'se cambió el equipo, se cierra ticket','2025-12-29 17:57:18'),(29,23,2,'nota de prueba','2025-12-29 18:21:36'),(30,25,9,'hola, esto es una nota de prueba','2025-12-30 12:01:47'),(31,28,2,'nota de prueba','2026-01-05 16:30:50'),(32,28,2,'nota de prueba','2026-01-06 09:58:00'),(33,27,2,'asdfasdfsadf','2026-01-06 10:12:15'),(34,27,2,'sadfasdfsa','2026-01-06 10:12:21'),(35,19,2,'sdfgsfgsdfgs','2026-01-06 10:40:22');
/*!40000 ALTER TABLE `notas_tickets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(100) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expira` datetime NOT NULL,
  `cerated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_resets`
--

LOCK TABLES `password_resets` WRITE;
/*!40000 ALTER TABLE `password_resets` DISABLE KEYS */;
INSERT INTO `password_resets` VALUES (5,'dmc5812@gmail.com','4ab12120838a3227c0d6e4b6a6649eff4ecd449e839ba58c85950a6e056bcd3319304c9b264fd47ea2735fcbb43440039d8a','2026-01-06 15:51:59','2026-01-06 13:51:59');
/*!40000 ALTER TABLE `password_resets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tickets`
--

DROP TABLE IF EXISTS `tickets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tickets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titulo` varchar(150) NOT NULL,
  `descripcion` text NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `agente_id` int(11) DEFAULT NULL,
  `prioridad` enum('baja','media','alta','critica') DEFAULT 'media',
  `estado` enum('abierto','en_proceso','espera_cliente','resuelto','cerrado') DEFAULT 'abierto',
  `fecha_creacion` datetime DEFAULT current_timestamp(),
  `fecha_actualizacion` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `departamento` varchar(50) DEFAULT NULL,
  `adjunto` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`),
  KEY `agente_id` (`agente_id`),
  CONSTRAINT `tickets_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tickets_ibfk_3` FOREIGN KEY (`agente_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tickets`
--

LOCK TABLES `tickets` WRITE;
/*!40000 ALTER TABLE `tickets` DISABLE KEYS */;
INSERT INTO `tickets` VALUES (6,'problema con pc','el pc explotó ',3,NULL,'alta','cerrado','2025-12-29 09:01:36','2025-12-29 09:04:48','Contabilidad',NULL),(7,'problemas con impresora','la impresora no imprime. ',3,NULL,'alta','resuelto','2025-12-29 09:06:18','2025-12-29 09:07:35','Recursos Humanos',NULL),(8,'correo de pruebas','Correo de pruebas ',2,NULL,'media','cerrado','2025-12-29 09:12:29','2025-12-29 15:07:57','TI',NULL),(9,'correo de pruebas','correo de pruebas',3,NULL,'media','abierto','2025-12-29 09:13:49',NULL,'Recursos Humanos',NULL),(10,'correo de pruebas','correo de pruebas ',3,NULL,'media','en_proceso','2025-12-29 09:15:49','2025-12-29 09:16:40','TI',NULL),(11,'123','123',3,NULL,'media','abierto','2025-12-29 09:21:00',NULL,'Contabilidad',NULL),(12,'123','123',3,NULL,'media','abierto','2025-12-29 09:21:22',NULL,'Contabilidad',NULL),(13,'123','123',3,NULL,'alta','abierto','2025-12-29 09:24:05',NULL,'TI',NULL),(14,'123','123',3,NULL,'media','en_proceso','2025-12-29 09:25:04','2025-12-29 09:44:18','TI',NULL),(15,'123','123',8,7,'media','cerrado','2025-12-29 10:36:04','2025-12-29 15:07:50','TI',NULL),(16,'123','ticket de prueba 2',7,7,'media','cerrado','2025-12-29 10:37:04','2025-12-29 15:07:39','TI',NULL),(17,'adsfasdf','asdfasdfas',8,7,'media','cerrado','2025-12-29 14:00:51','2025-12-29 14:15:35','Recursos Humanos',NULL),(18,'adfadsfasdf','asdfasdfasdf',8,7,'media','cerrado','2025-12-29 14:37:57','2025-12-29 14:38:40','Operaciones',NULL),(19,'fsdfasd','asdfasdfa',2,7,'media','cerrado','2025-12-29 14:53:25','2025-12-29 15:07:32','Contabilidad',NULL),(20,'fsdfasdfa','asdfasdf',8,7,'media','cerrado','2025-12-29 15:20:10','2025-12-29 17:57:45','Contabilidad',NULL),(21,'el pc explotó','Estaba usando el equipo, pero le empezó a salir humo y explotó',8,7,'alta','cerrado','2025-12-29 16:42:30','2025-12-29 16:46:24','Ventas','1767037350_Der8auerreviveunaGeForceRTX2070SUPERquemada1740x416.jpg'),(22,'Computador sin internet.','equipo presenta intermitencia de red',7,7,'media','cerrado','2025-12-29 16:49:07','2025-12-29 17:57:55','Ventas',NULL),(23,'dasfasdfsa','asdfasdfas',7,7,'media','cerrado','2025-12-29 16:53:59','2025-12-29 16:58:37','Ventas',NULL),(24,'se me quemó el pc','el pc explotó de la nada',8,7,'media','cerrado','2025-12-29 17:38:49','2025-12-29 17:57:27','Operaciones','1767040729_Der8auerreviveunaGeForceRTX2070SUPERquemada1740x416.jpg'),(25,'Computador sin internet.','otro ticket de prueba',8,9,'media','abierto','2025-12-30 12:00:42',NULL,'TI',NULL),(26,'fgsdfgsdf','dsfgsdfgdsf',8,9,'baja','abierto','2025-12-30 12:35:47',NULL,'Recursos Humanos',NULL),(27,'fgsdfgsdfgs','sdfgsdfgdsfgs',8,9,'alta','cerrado','2025-12-30 12:36:05','2026-01-06 10:12:27','Operaciones',NULL),(28,'asdfasdfa','asdfasdfa',8,9,'media','cerrado','2026-01-05 16:30:19','2026-01-06 09:52:14','Operaciones',NULL);
/*!40000 ALTER TABLE `tickets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` varchar(50) DEFAULT 'cliente',
  `avatar` varchar(255) DEFAULT 'default.png',
  `activo` tinyint(1) DEFAULT 1,
  `fecha_creacion` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (2,'Administrador','admin@tickets.com','$2y$10$3Nal6ExErloCBDsL8iAhr.Q.iG3oO2ANHJ4HWeqMNn9GFvgBYqfM2','admin','default.png',1,'2025-12-22 16:50:20'),(7,'Diego Cifuentes','mdiego324@gmail.com','$2y$10$d5hXSeouIQIZh7LeHxCjpe/pj9YI.vCOvVj.QAxDauCfe.omA8jBC','tecnico','default.png',0,'2025-12-29 10:33:43'),(8,'Alonso Molina','dmc5812@gmail.com','$2y$10$SAAIrbDfSBY9nzQ6.DOa9uiaO6vZxYQGDdZGjsNWitB9f6zuh96XC','usuario','default.png',1,'2025-12-29 10:34:00'),(9,'Diego Molina','diegomolina@dac-controls.com','$2y$10$DE49MlILbLXXwAxjqKQ0ouss5el0KSoJkWQraA3gg9o9xFF8sId.y','tecnico','default.png',1,'2025-12-30 08:35:01'),(10,'Iván Paredes Trujillo','ivanparedes@dac-controls.com','$2y$10$Do6B4BE.xBYo9QgVRGM1EuK11r5r3ejXodOGSFdvefBm6ypv2UJke','admin','default.png',1,'2025-12-30 11:58:29');
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-01-06 12:17:11
