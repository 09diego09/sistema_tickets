-- MySQL dump 10.13  Distrib 8.0.44, for Win64 (x86_64)
--
-- Host: localhost    Database: sistema_tickets
-- ------------------------------------------------------
-- Server version	9.5.0-commercial

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
SET @MYSQLDUMP_TEMP_LOG_BIN = @@SESSION.SQL_LOG_BIN;
SET @@SESSION.SQL_LOG_BIN= 0;

--
-- GTID state at the beginning of the backup 
--

SET @@GLOBAL.GTID_PURGED=/*!80000 '+'*/ 'e13acd1b-dcd3-11f0-9143-b07b256577a6:1-80';

--
-- Table structure for table `tickets`
--

DROP TABLE IF EXISTS `tickets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tickets` (
  `id` int NOT NULL AUTO_INCREMENT,
  `titulo` varchar(150) NOT NULL,
  `descripcion` text NOT NULL,
  `departamento` varchar(100) DEFAULT NULL,
  `telefono_contacto` varchar(50) DEFAULT NULL,
  `email_contacto` varchar(100) DEFAULT NULL,
  `usuario_id` int NOT NULL,
  `categoria_id` int DEFAULT NULL,
  `agente_id` int DEFAULT NULL,
  `prioridad` enum('baja','media','alta','critica') DEFAULT 'media',
  `estado` varchar(50) NOT NULL DEFAULT 'abierto',
  `fecha_creacion` datetime DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`),
  KEY `categoria_id` (`categoria_id`),
  KEY `agente_id` (`agente_id`),
  CONSTRAINT `tickets_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tickets_ibfk_2` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`),
  CONSTRAINT `tickets_ibfk_3` FOREIGN KEY (`agente_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tickets`
--

LOCK TABLES `tickets` WRITE;
/*!40000 ALTER TABLE `tickets` DISABLE KEYS */;
INSERT INTO `tickets` VALUES (1,'monitor no funciona','mi monitor no prende, ya revisamos que esté bien conectado','RRHH','945685320','usuario@ejemplo.com',7,NULL,NULL,'alta','cerrado','2025-12-23 09:19:29','2025-12-23 09:35:00'),(2,'monitor no funciona','gfgsdfgsdfsg','TI','945685320','usuario@ejemplo.com',7,NULL,6,'alta','cerrado','2025-12-23 09:34:01','2025-12-23 10:49:14'),(3,'problemas con internet','El internet está con intermitencias, lleva cayéndose, después funciona y se vuelve a caer','Contabilidad','+854654','usuario@ejemplo.com',7,NULL,NULL,'alta','abierto','2025-12-23 10:04:13',NULL),(4,'herramientas rotas','Tuvimos complicaciones y se nos rompieron algunas tenazas necesarias para el trabajo.','TI','+56945685320','tecnico@ejemplo.com',6,NULL,NULL,'alta','abierto','2025-12-23 10:05:00',NULL),(5,'problemas con facturas','empresa rechaza factura reiteradamente','Administración','7454654654','admin@tickets.com',5,NULL,NULL,'media','cerrado','2025-12-23 10:06:28','2025-12-23 10:31:33'),(6,'problemas con conexión','el internet está caído','Contabilidad','68464654684','usuario@ejemplo.com',7,NULL,NULL,'alta','abierto','2025-12-23 10:56:30',NULL),(7,'Computador sin internet.','fsdgsdfgs','Contabilidad','sdfgsdfgsd','usuario@ejemplo.com',7,NULL,NULL,'media','abierto','2025-12-23 10:58:54',NULL),(8,'weqweq','qweqweqweqwe','Contabilidad','945685320','usuario@ejemplo.com',7,NULL,NULL,'critica','abierto','2025-12-23 11:04:57',NULL),(9,'sfgsgsdfgsdfgs','dfgsdfgsdfgsfg','TI','32413412341341','usuario@ejemplo.com',7,NULL,NULL,'media','abierto','2025-12-23 11:07:11',NULL),(10,'sdfsgsfgs','fgsdfgsdfgs','Contabilidad','42134113123','admin@ticket.com',5,NULL,NULL,'baja','abierto','2025-12-23 11:09:18',NULL),(11,'Computador sin internet.','rtgwertwertwrwetwert','Contabilidad','1542352345234','admin@tickets.com',5,NULL,6,'media','abierto','2025-12-23 11:10:11',NULL),(12,'dsdfasdfasd','asdfasdfasdfas','TI','313143141324','admin@tickets.com',5,NULL,6,'media','abierto','2025-12-23 11:12:37',NULL),(13,'adsfasdfa','asdfasdfas','Recursos Humanos','asdfasdfa','admin@tickets.com',5,NULL,6,'media','abierto','2025-12-23 11:12:58',NULL),(14,'dfasdfasdf','asdfasdfasd','TI','324123412341','admin@tickets.com',5,NULL,6,'media','abierto','2025-12-23 11:13:32',NULL),(15,'fsdfgsdfgsdfg','dsfgsdfgsdfgsdf','Contabilidad','24353452345','admin@tickets.com',5,NULL,8,'media','abierto','2025-12-23 11:13:44',NULL),(16,'fgsdfgsdfgs','sdfgsdfgsdfg','Recursos Humanos','123413241243','admin@tickets.com',5,NULL,9,'media','cerrado','2025-12-23 11:13:59','2025-12-23 11:15:11'),(17,'lkugjghjhglih','kljgkjghjh','Recursos Humanos','453434645646','admin@tickets.com',5,NULL,6,'critica','en_proceso','2025-12-23 11:17:50','2025-12-23 11:21:17');
/*!40000 ALTER TABLE `tickets` ENABLE KEYS */;
UNLOCK TABLES;
SET @@SESSION.SQL_LOG_BIN = @MYSQLDUMP_TEMP_LOG_BIN;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-12-23 11:31:43
