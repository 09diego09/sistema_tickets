-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Servidor: sql302.infinityfree.com
-- Tiempo de generación: 29-01-2026 a las 10:25:46
-- Versión del servidor: 11.4.9-MariaDB
-- Versión de PHP: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `if0_40858595_sistema_tickets`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notas_tickets`
--

CREATE TABLE `notas_tickets` (
  `id` int(11) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `nota` text NOT NULL,
  `fecha` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `notas_tickets`
--

INSERT INTO `notas_tickets` (`id`, `ticket_id`, `usuario_id`, `nota`, `fecha`) VALUES
(1, 1, 8, 'revisamos el cableado y está todo en orden, fue error de software, ya se arregló el problema de la impresora', '2026-01-08 13:39:07'),
(2, 1, 8, 'revisamos el cableado y está todo en orden, fue error de software, ya se arregló el problema de la impresora', '2026-01-08 13:39:10'),
(3, 1, 8, 'problema solucionado, se cierra ticket', '2026-01-08 13:39:27'),
(4, 2, 8, 'la impresión de prueba sale en orden, se prueban otras alternativas', '2026-01-08 11:15:02');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expira` datetime NOT NULL,
  `cerated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `password_resets`
--

INSERT INTO `password_resets` (`id`, `email`, `token`, `expira`, `cerated_at`) VALUES
(5, 'dmc5812@gmail.com', '4ab12120838a3227c0d6e4b6a6649eff4ecd449e839ba58c85950a6e056bcd3319304c9b264fd47ea2735fcbb43440039d8a', '2026-01-06 15:51:59', '2026-01-06 13:51:59'),
(6, 'ashleycristina2002@gmail.com', 'a418194fda00eee80fcc15cf164da3051e708a1556b755c7267164bbb8c2e06a63086059f189cc668d45491301a19fd84168', '2026-01-06 20:06:39', '2026-01-06 18:06:39'),
(9, 'diegomolina@dac-controls.com', '47bbc0badb8df963d5cf154a69776942aef8f3a19288e7e021bc58db00263c0f9bf679f88edb0eca5cb95f225febf68df3ba', '2026-01-12 09:17:57', '2026-01-12 13:17:57');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tareas`
--

CREATE TABLE `tareas` (
  `id` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `usuario_asignado_id` int(11) NOT NULL,
  `creador_id` int(11) NOT NULL,
  `completada` tinyint(1) DEFAULT 0,
  `fecha_creacion` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tareas`
--

INSERT INTO `tareas` (`id`, `titulo`, `usuario_asignado_id`, `creador_id`, `completada`, `fecha_creacion`) VALUES
(25, 'cambiar perifericos', 9, 9, 0, '2026-01-08 11:31:38');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tickets`
--

CREATE TABLE `tickets` (
  `id` int(11) NOT NULL,
  `titulo` varchar(150) NOT NULL,
  `descripcion` text NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `agente_id` int(11) DEFAULT NULL,
  `prioridad` enum('baja','media','alta','critica') DEFAULT 'media',
  `estado` enum('abierto','en_proceso','espera_cliente','resuelto','cerrado') DEFAULT 'abierto',
  `fecha_creacion` datetime DEFAULT current_timestamp(),
  `fecha_actualizacion` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `departamento` varchar(50) DEFAULT NULL,
  `adjunto` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tickets`
--

INSERT INTO `tickets` (`id`, `titulo`, `descripcion`, `usuario_id`, `agente_id`, `prioridad`, `estado`, `fecha_creacion`, `fecha_actualizacion`, `departamento`, `adjunto`) VALUES
(1, 'Fallo en la impresora principal.', 'la impresora dejó de imprimir de repente, no sabemos qué pueda ser.', 7, 8, 'alta', 'cerrado', '2026-01-08 13:36:58', '2026-01-08 13:39:34', 'Recursos Humanos', NULL),
(2, 'impresora con problemas', 'la impresora imprime raro', 7, 8, 'media', 'cerrado', '2026-01-08 10:45:52', '2026-01-20 05:58:28', 'Contabilidad', NULL),
(3, 'problemas con pc', 'Mi pc tiene temperaturas muy altas y a ratos se apaga', 7, 8, 'media', 'abierto', '2026-01-28 11:57:07', NULL, 'Administración', '1769630227_imagen20260128165705020.png');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ticket_checklist`
--

CREATE TABLE `ticket_checklist` (
  `id` int(11) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `titulo_tarea` varchar(255) NOT NULL,
  `completado` tinyint(1) DEFAULT 0,
  `fecha_creacion` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ticket_checklist`
--

INSERT INTO `ticket_checklist` (`id`, `ticket_id`, `titulo_tarea`, `completado`, `fecha_creacion`) VALUES
(2, 2, 'revisar estado de impresión con impresiones de prueba', 0, '2026-01-08 11:13:23');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `tel_usuarios` varchar(50) DEFAULT NULL,
  `rut_usuarios` varchar(50) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` varchar(50) DEFAULT 'cliente',
  `avatar` varchar(255) DEFAULT 'default.png',
  `activo` tinyint(1) DEFAULT 1,
  `fecha_creacion` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `tel_usuarios`, `rut_usuarios`, `email`, `password`, `rol`, `avatar`, `activo`, `fecha_creacion`) VALUES
(2, 'Administrador', '+56945685320', '19958814-9', 'admin@tickets.com', '$2y$10$3Nal6ExErloCBDsL8iAhr.Q.iG3oO2ANHJ4HWeqMNn9GFvgBYqfM2', 'admin', 'default.png', 1, '2025-12-22 16:50:20'),
(7, 'Diego Cifuentes', NULL, NULL, 'mdiego324@gmail.com', '$2y$10$t5IYHWIkfHPqVqluYvKqVeHd2cLKaMdCxOhZTQFoywSxfG5E8T7gW', 'usuario', 'default.png', 1, '2025-12-29 10:33:43'),
(8, 'Diego Alonso Molina Cifuentes', '(+569)45685320', '19958814-1', 'dmc5812@gmail.com', '$2y$10$0SBD/jZ.5Q4B4tQkQkUbpeMqrSL6mzCiAhGOi1E0f053t8/5PKoHW', 'tecnico', 'default.png', 1, '2025-12-29 10:34:00'),
(9, 'Diego Molina', NULL, NULL, 'diegomolina@dac-controls.com', '$2y$10$DE49MlILbLXXwAxjqKQ0ouss5el0KSoJkWQraA3gg9o9xFF8sId.y', 'admin', 'default.png', 1, '2025-12-30 08:35:01'),
(10, 'Iván Ilich Paredes Trujillo', '(+569)92040523', '17841198-5', 'ivanparedes@dac-controls.com', '$2y$10$Do6B4BE.xBYo9QgVRGM1EuK11r5r3ejXodOGSFdvefBm6ypv2UJke', 'admin', 'default.png', 1, '2025-12-30 11:58:29'),
(11, 'Joaquin Alberto Cañete Lopez', '(+56 9) 45870321', '20631999-2', 'joaquin.cl.482@gmail.com', '$2y$10$Wye1khi/Sq0HlgGanR75PeOIU5Tz0xqjCxtHA9OuU2ZH/oEuLQRpu', 'usuario', 'default.png', 1, '2026-01-06 14:44:33'),
(12, 'Gregory Enmanuel Contreras Delgado', '(+569)7288862', '27415693-7', 'gregorycontreras@dac-controls.com', '$2y$10$Wye1khi/Sq0HlgGanR75PeOIU5Tz0xqjCxtHA9OuU2ZH/oEuLQRpu', 'usuario', 'default.png', 1, '2026-01-06 14:44:33'),
(13, 'Gabriel Alejandro Diaz Castro', '(+569) 47167614', '17014173-3', 'gabrieldiazcastro.1988@gmail.com', '$2y$10$Wye1khi/Sq0HlgGanR75PeOIU5Tz0xqjCxtHA9OuU2ZH/oEuLQRpu', 'usuario', 'default.png', 1, '2026-01-06 14:44:33'),
(14, 'Leonardo Andres Farias Gomez', '(+569) 53449148', '18486867-9', 'lfg2093@gmail.com', '$2y$10$Wye1khi/Sq0HlgGanR75PeOIU5Tz0xqjCxtHA9OuU2ZH/oEuLQRpu', 'usuario', 'default.png', 1, '2026-01-06 14:44:33'),
(15, 'David Nibaldo Escorza Quiroz', '(+56 9)7337 7472', '10979211-k', 'davidescorza@dac-controls.com', '$2y$10$Wye1khi/Sq0HlgGanR75PeOIU5Tz0xqjCxtHA9OuU2ZH/oEuLQRpu', 'admin', 'default.png', 1, '2026-01-06 14:44:33'),
(16, 'Ricardo Saul Troquian Boettcher', '(+569)88079230', '16670786-2', 'ricardotroquian@dac-controls.com', '$2y$10$Wye1khi/Sq0HlgGanR75PeOIU5Tz0xqjCxtHA9OuU2ZH/oEuLQRpu', 'usuario', 'default.png', 1, '2026-01-06 14:44:33'),
(17, 'Cristian Javier Troquian Boettcher', '(+569)88079230', '18363744-4', 'cristiantroquian@dac.controls.com', '$2y$10$Wye1khi/Sq0HlgGanR75PeOIU5Tz0xqjCxtHA9OuU2ZH/oEuLQRpu', 'usuario', 'default.png', 1, '2026-01-06 14:44:33'),
(18, 'Luis Edmundo Arias Camus', '(+569)92000148', '10514207-2', 'luisarias@dac-controls.com', '$2y$10$Wye1khi/Sq0HlgGanR75PeOIU5Tz0xqjCxtHA9OuU2ZH/oEuLQRpu', 'usuario', 'default.png', 1, '2026-01-06 14:44:33'),
(19, 'Patricio Javier Gallardo Morales', '(+569)92749633', '11592155-k', 'patriciogallardo@dc-controls.com', '$2y$10$Wye1khi/Sq0HlgGanR75PeOIU5Tz0xqjCxtHA9OuU2ZH/oEuLQRpu', 'usuario', 'default.png', 1, '2026-01-06 14:44:33'),
(20, 'Sarich Abraham Gonzalez Venegas', '(+569)73235321', '18049085-k', 'sarichgonzalez@dac-controls.com', '$2y$10$Wye1khi/Sq0HlgGanR75PeOIU5Tz0xqjCxtHA9OuU2ZH/oEuLQRpu', 'usuario', 'default.png', 1, '2026-01-06 14:44:33'),
(21, 'Arlett Lara Alvarez', '(+569)96564486', '7848655-4', 'arlettlara@dac-controls.com', '$2y$10$Wye1khi/Sq0HlgGanR75PeOIU5Tz0xqjCxtHA9OuU2ZH/oEuLQRpu', 'usuario', 'default.png', 1, '2026-01-06 14:44:33'),
(22, 'Sebastian Ignacio Duran Ortiz', '(+569)94180040', '18547025-3', 'sebastianduran@dac-controls.com', '$2y$10$Wye1khi/Sq0HlgGanR75PeOIU5Tz0xqjCxtHA9OuU2ZH/oEuLQRpu', 'usuario', 'default.png', 1, '2026-01-06 14:44:33'),
(23, 'Raul Ernesto Jimenez Mujica', '(+569)48170035', '26418128-3', 'rauljimenez@dac-controls.com', '$2y$10$Wye1khi/Sq0HlgGanR75PeOIU5Tz0xqjCxtHA9OuU2ZH/oEuLQRpu', 'usuario', 'default.png', 1, '2026-01-06 14:44:33'),
(24, 'Daniel Alberto Rondon Valenzuela', '(+569)35087890', '26519307-2', 'danielrondon@dac-controls.com', '$2y$10$Wye1khi/Sq0HlgGanR75PeOIU5Tz0xqjCxtHA9OuU2ZH/oEuLQRpu', 'usuario', 'default.png', 1, '2026-01-06 14:44:33'),
(25, 'Dely Oriana Moreno Rosales', '(+569)59345208', '27072261-k', 'delymoreno@dac-controls.com', '$2y$10$Wye1khi/Sq0HlgGanR75PeOIU5Tz0xqjCxtHA9OuU2ZH/oEuLQRpu', 'usuario', 'default.png', 1, '2026-01-06 14:44:33'),
(26, 'Emil Jesus Ramirez Ramos', '(+569)84521708', '26743002-0', 'emilramirez@dac-controls.com', '$2y$10$Wye1khi/Sq0HlgGanR75PeOIU5Tz0xqjCxtHA9OuU2ZH/oEuLQRpu', 'usuario', 'default.png', 1, '2026-01-06 14:44:33'),
(27, 'Ronald Ramon Arzola Rendon', '(+569)40380975', '26762538-7', 'ronaldarzola@dac-controls.com', '$2y$10$Wye1khi/Sq0HlgGanR75PeOIU5Tz0xqjCxtHA9OuU2ZH/oEuLQRpu', 'usuario', 'default.png', 1, '2026-01-06 14:44:33'),
(29, 'Leonardo David Rondon Valenzuela', '(+569)58519268', '26519303-k', 'leonardorondon@dac-controls.com', '$2y$10$Wye1khi/Sq0HlgGanR75PeOIU5Tz0xqjCxtHA9OuU2ZH/oEuLQRpu', 'usuario', 'default.png', 1, '2026-01-06 14:44:33'),
(30, 'Eduar Jesus Brito Mundarain', '(+569)32920270', '26889891-3', 'eduarbrito@dac-controls.com', '$2y$10$Wye1khi/Sq0HlgGanR75PeOIU5Tz0xqjCxtHA9OuU2ZH/oEuLQRpu', 'usuario', 'default.png', 1, '2026-01-06 14:44:33'),
(31, 'Camila Fernanda Cobo Reyes', '(+569)56595915', '18945636-0', 'camilacobo@dac-controls.com', '$2y$10$Wye1khi/Sq0HlgGanR75PeOIU5Tz0xqjCxtHA9OuU2ZH/oEuLQRpu', 'usuario', 'default.png', 1, '2026-01-06 14:44:33'),
(32, 'Johan Neptaly Rangel Toro', '(+569)71785646', '26943547-k', 'johanrangel@dac-controls.com', '$2y$10$Wye1khi/Sq0HlgGanR75PeOIU5Tz0xqjCxtHA9OuU2ZH/oEuLQRpu', 'usuario', 'default.png', 1, '2026-01-06 14:44:33'),
(33, 'Ivan Jose Rivas Barroso', '(+569)36874512', '27023316-3', 'ivanrivas@dac-controls.com', '$2y$10$Wye1khi/Sq0HlgGanR75PeOIU5Tz0xqjCxtHA9OuU2ZH/oEuLQRpu', 'usuario', 'default.png', 1, '2026-01-06 14:44:33'),
(34, 'Carlos Javier Duran Carrero', '(+569)56819091', '26494384-1', 'carlosduran@dac-controls.com', '$2y$10$Wye1khi/Sq0HlgGanR75PeOIU5Tz0xqjCxtHA9OuU2ZH/oEuLQRpu', 'usuario', 'default.png', 1, '2026-01-06 14:44:33'),
(35, 'Luis Efraín Altriaga Ramirez', '(+569)67254667', '26342464-6', 'luis.altriaga151922@gmail.com', '$2y$10$Wye1khi/Sq0HlgGanR75PeOIU5Tz0xqjCxtHA9OuU2ZH/oEuLQRpu', 'usuario', 'default.png', 1, '2026-01-06 14:44:33'),
(36, 'Miguel Ángel Gonzalez Salgado', '(+569)88244888', '16471451-9', 'miguelgonzalez@dac-controls.com', '$2y$10$Wye1khi/Sq0HlgGanR75PeOIU5Tz0xqjCxtHA9OuU2ZH/oEuLQRpu', 'usuario', 'default.png', 1, '2026-01-06 14:44:33'),
(37, 'Ivan Enrique Garces Ossa', '(+569)73603470', '19047924-2', 'ivangarces@dac-controls.com', '$2y$10$Wye1khi/Sq0HlgGanR75PeOIU5Tz0xqjCxtHA9OuU2ZH/oEuLQRpu', 'usuario', 'default.png', 1, '2026-01-06 14:44:33'),
(38, 'Leonardo Alexis Echeverria Mellado', '(+569)44293704', '14.605.981-3', 'leonardoecheverria@dac-controls.com', '$2y$10$Wye1khi/Sq0HlgGanR75PeOIU5Tz0xqjCxtHA9OuU2ZH/oEuLQRpu', 'usuario', 'default.png', 1, '2026-01-06 14:44:33'),
(39, 'Johan Sebastian  Mansini Zuñiga', '(+569)990530292', '20712106-1', 'colojohancolo@gmail.com', '$2y$10$Wye1khi/Sq0HlgGanR75PeOIU5Tz0xqjCxtHA9OuU2ZH/oEuLQRpu', 'usuario', 'default.png', 1, '2026-01-06 14:44:33'),
(40, 'Javier Alejandro Rondon Salas', '(+569)47135703', '26770781-2', 'javierrondon@dac-controls.com', '$2y$10$Wye1khi/Sq0HlgGanR75PeOIU5Tz0xqjCxtHA9OuU2ZH/oEuLQRpu', 'usuario', 'default.png', 1, '2026-01-06 14:44:33'),
(41, 'Carli Daniela Sanchez Roa', '(+569)37360248', '28326192-1', 'carlisanchez@dac-controls.com', '$2y$10$Wye1khi/Sq0HlgGanR75PeOIU5Tz0xqjCxtHA9OuU2ZH/oEuLQRpu', 'usuario', 'default.png', 1, '2026-01-06 14:44:33'),
(42, 'Danixa Ximena Martinez Muñoz', '(+569)61544302', '18717716-2', 'danixamartinez@dac-controls.com', '$2y$10$Wye1khi/Sq0HlgGanR75PeOIU5Tz0xqjCxtHA9OuU2ZH/oEuLQRpu', 'usuario', 'default.png', 1, '2026-01-06 14:44:33'),
(43, 'Bastian Tomas Quintulaf Quintulaf', '(+569)41145203', '19554588K', 'bastiian117@gmail.com', '$2y$10$Wye1khi/Sq0HlgGanR75PeOIU5Tz0xqjCxtHA9OuU2ZH/oEuLQRpu', 'usuario', 'default.png', 1, '2026-01-06 14:44:33'),
(44, 'Reinaldo Jose Hevia Barrientos', '(+569)57867881', '27720871-7', 'reinaldohevia@dac-controls.com', '$2y$10$Wye1khi/Sq0HlgGanR75PeOIU5Tz0xqjCxtHA9OuU2ZH/oEuLQRpu', 'usuario', 'default.png', 1, '2026-01-06 14:44:33'),
(45, 'Cristian Andres Pino Becerra', '(+569)32661830', '17470924-6', 'cristianpino@dac-controls.com', '$2y$10$Wye1khi/Sq0HlgGanR75PeOIU5Tz0xqjCxtHA9OuU2ZH/oEuLQRpu', 'usuario', 'default.png', 1, '2026-01-06 14:44:33'),
(46, 'Fernando Ibrahin Ruiz Castellanos', '(+569)86268232', '27909791-2', 'pit28294@gmail.com', '$2y$10$Wye1khi/Sq0HlgGanR75PeOIU5Tz0xqjCxtHA9OuU2ZH/oEuLQRpu', 'usuario', 'default.png', 1, '2026-01-06 14:44:33'),
(47, 'Jose Luis Sarmiento Paredes', '(+569)72089715', '25801856-7', 'joselsarmiento81@gmail.com', '$2y$10$Wye1khi/Sq0HlgGanR75PeOIU5Tz0xqjCxtHA9OuU2ZH/oEuLQRpu', 'usuario', 'default.png', 1, '2026-01-06 14:44:33'),
(48, 'Hugo Ernesto Rodríguez Bracho', '(+569)46734819', '25663046-k', 'Hugo.rodriguez.bracho@gmail.com', '$2y$10$Wye1khi/Sq0HlgGanR75PeOIU5Tz0xqjCxtHA9OuU2ZH/oEuLQRpu', 'usuario', 'default.png', 1, '2026-01-06 14:44:33'),
(49, 'Alberth Alexander Abreu Gonzalez', '(+569)76960001', '26597552-6', 'alberthabreu@dac-controls.com', '$2y$10$Wye1khi/Sq0HlgGanR75PeOIU5Tz0xqjCxtHA9OuU2ZH/oEuLQRpu', 'usuario', 'default.png', 1, '2026-01-06 14:44:33'),
(50, 'Darío Adrián Jiménez Uribe', '(+569)45584176', '17007786-5', 'juribe.dario08@gmail.com', '$2y$10$Wye1khi/Sq0HlgGanR75PeOIU5Tz0xqjCxtHA9OuU2ZH/oEuLQRpu', 'usuario', 'default.png', 1, '2026-01-06 14:44:33'),
(51, 'Agustin Alejandro Gaete Muñoz', '(+569)64629323', '20780760-5', 'Ag.gaete@duoc.cl', '$2y$10$Wye1khi/Sq0HlgGanR75PeOIU5Tz0xqjCxtHA9OuU2ZH/oEuLQRpu', 'usuario', 'default.png', 1, '2026-01-06 14:44:33'),
(52, 'Luis Mauricio Gonzalez Fre', '(+569)82593778', '12863596-3', 'luisgonzalezfre@yahoo.com', '$2y$10$Wye1khi/Sq0HlgGanR75PeOIU5Tz0xqjCxtHA9OuU2ZH/oEuLQRpu', 'usuario', 'default.png', 1, '2026-01-06 14:44:33'),
(53, 'Abel Ernesto Garcia Trujillo', '(+569)953021430', '25852480-2', 'imation_ab@hotmail.com', '$2y$10$Wye1khi/Sq0HlgGanR75PeOIU5Tz0xqjCxtHA9OuU2ZH/oEuLQRpu', 'usuario', 'default.png', 1, '2026-01-06 14:44:33'),
(54, 'Marcelo Ignacio Araos Bravo', '(+569)30807678', '20468777-3', 'marcelo.araosbravo@gmail.com', '$2y$10$Wye1khi/Sq0HlgGanR75PeOIU5Tz0xqjCxtHA9OuU2ZH/oEuLQRpu', 'usuario', 'default.png', 1, '2026-01-06 14:44:33'),
(55, 'Sebastian Ulises Garcia Muñoz', '(+569)54196669', '18927479-3', 'se.garciam94@gmail.com', '$2y$10$Wye1khi/Sq0HlgGanR75PeOIU5Tz0xqjCxtHA9OuU2ZH/oEuLQRpu', 'usuario', 'default.png', 1, '2026-01-06 14:44:33'),
(56, 'Joel Esteban Hernandez Parra', '(+569)42017887', '265735045', 'hernandezparrajoel471@gmail.com', '$2y$10$Wye1khi/Sq0HlgGanR75PeOIU5Tz0xqjCxtHA9OuU2ZH/oEuLQRpu', 'usuario', 'default.png', 1, '2026-01-06 14:44:33'),
(57, 'Enrique Javier Sosa Clermont', '(+569)57218938', '272057222', 'ejsosachile@gmail.com', '$2y$10$Wye1khi/Sq0HlgGanR75PeOIU5Tz0xqjCxtHA9OuU2ZH/oEuLQRpu', 'usuario', 'default.png', 1, '2026-01-06 14:44:33'),
(58, 'Tomas Facundo Gallardo Villalobos', '(+569)54951458', '20224452-1', 'gallardo.tomasf@gmail.com', '$2y$10$Wye1khi/Sq0HlgGanR75PeOIU5Tz0xqjCxtHA9OuU2ZH/oEuLQRpu', 'usuario', 'default.png', 1, '2026-01-06 14:44:33'),
(59, 'Jorge Leonardo Rosales', '(+569) 56365333', '267325766', 'jorgerosales29@gmail.com', '$2y$10$Wye1khi/Sq0HlgGanR75PeOIU5Tz0xqjCxtHA9OuU2ZH/oEuLQRpu', 'usuario', 'default.png', 1, '2026-01-06 14:44:33'),
(60, 'Javier Rodrigo Toledo Ulloa', '(+569)42467985', '17097593-6', 'xjavier.toledo@gmail.com', '$2y$10$Wye1khi/Sq0HlgGanR75PeOIU5Tz0xqjCxtHA9OuU2ZH/oEuLQRpu', 'usuario', 'default.png', 1, '2026-01-06 14:44:33'),
(61, 'Guillermo Jesús Galban Montiel', '(+569) 98787670', '268472789', 'ggalbancbz@gmail.com', '$2y$10$Wye1khi/Sq0HlgGanR75PeOIU5Tz0xqjCxtHA9OuU2ZH/oEuLQRpu', 'usuario', 'default.png', 1, '2026-01-06 14:44:33'),
(62, 'Benjamín Sebastian Peña Baeza', '(+569)98642142', '202807666', 'benjahiphop16@gmail.com', '$2y$10$Wye1khi/Sq0HlgGanR75PeOIU5Tz0xqjCxtHA9OuU2ZH/oEuLQRpu', 'usuario', 'default.png', 1, '2026-01-06 14:44:33'),
(63, 'Bruno Matías Garrido Valdivia', '(+569)45605120', '209543036', 'bruno.garrido619@gmail.com', '$2y$10$Wye1khi/Sq0HlgGanR75PeOIU5Tz0xqjCxtHA9OuU2ZH/oEuLQRpu', 'usuario', 'default.png', 1, '2026-01-06 14:44:33'),
(64, 'Daniel De Jesús Hernandez Morales', '(+569)28339779', '28171587-9', 'jesusmorales7007@gmail.com', '$2y$10$Wye1khi/Sq0HlgGanR75PeOIU5Tz0xqjCxtHA9OuU2ZH/oEuLQRpu', 'usuario', 'default.png', 1, '2026-01-06 14:44:33'),
(65, 'Jorge Eduardo muñoz Gonzalez', '(+569)64335877', '180542086', 'j.munoz.gz25@gmail.com', '$2y$10$Wye1khi/Sq0HlgGanR75PeOIU5Tz0xqjCxtHA9OuU2ZH/oEuLQRpu', 'usuario', 'default.png', 1, '2026-01-06 14:44:33'),
(66, 'Jonathan Francisco Sanchez Escobar', '(+569)97185935', '158883155', 'jonasmiths777@gmail.com', '$2y$10$Wye1khi/Sq0HlgGanR75PeOIU5Tz0xqjCxtHA9OuU2ZH/oEuLQRpu', 'usuario', 'default.png', 1, '2026-01-06 14:44:33'),
(67, 'Jesus Efraín Rios Mendoza', '(+569)55265362', '27586693-8', 'jriomen28@gmail.com', '$2y$10$Wye1khi/Sq0HlgGanR75PeOIU5Tz0xqjCxtHA9OuU2ZH/oEuLQRpu', 'usuario', 'default.png', 1, '2026-01-06 14:44:33'),
(68, 'Lucas Martin  Guzman Bravo', '(+569)931101716', '20631387-0', 'lucas.122bravo@gmail.com', '$2y$10$Wye1khi/Sq0HlgGanR75PeOIU5Tz0xqjCxtHA9OuU2ZH/oEuLQRpu', 'usuario', 'default.png', 1, '2026-01-06 14:44:33'),
(69, 'Ashley Cristina González Sanchez', '(+569)91272197', '26.271.976-6', 'ashleycristina2002@gmail.com', '$2y$10$Wye1khi/Sq0HlgGanR75PeOIU5Tz0xqjCxtHA9OuU2ZH/oEuLQRpu', 'usuario', 'default.png', 1, '2026-01-06 14:44:33'),
(70, 'Juan Gabriel Leiva Catalan', '(+569)955102366', '17340442-5', 'j.1990leiva@gmail.com.com', '$2y$10$Wye1khi/Sq0HlgGanR75PeOIU5Tz0xqjCxtHA9OuU2ZH/oEuLQRpu', 'usuario', 'default.png', 1, '2026-01-06 14:44:33'),
(71, 'Jose Benjamín Medina Ruz', '(+569)28465680', '21873407-3', 'medinabenja179@gmail.com', '$2y$10$Wye1khi/Sq0HlgGanR75PeOIU5Tz0xqjCxtHA9OuU2ZH/oEuLQRpu', 'usuario', 'default.png', 1, '2026-01-06 14:44:33'),
(72, 'Diego Danilo Montecinos Henriquez', '(+569)68780204', '16986186-2', 'dhenriquezm11@gmail.com', '$2y$10$Wye1khi/Sq0HlgGanR75PeOIU5Tz0xqjCxtHA9OuU2ZH/oEuLQRpu', 'usuario', 'default.png', 1, '2026-01-06 14:44:33'),
(73, 'Claudio Andres Muñoz Garcia', '(+569)67059061', '13682002-8', 'clgarcia@live.cl', '$2y$10$Wye1khi/Sq0HlgGanR75PeOIU5Tz0xqjCxtHA9OuU2ZH/oEuLQRpu', 'usuario', 'default.png', 1, '2026-01-06 14:44:33'),
(74, 'Martin Ignacio Barra Muñoz', '(+569)39379672', '22360549-4', 'martin.barramuñoz17@gmail.com', '$2y$10$Wye1khi/Sq0HlgGanR75PeOIU5Tz0xqjCxtHA9OuU2ZH/oEuLQRpu', 'usuario', 'default.png', 1, '2026-01-06 14:44:33'),
(75, 'Ruben Dario Gomez Saez', '(+569)41026331', '25855022-6', 'rubengomez@dac-controls.com', '$2y$10$Wye1khi/Sq0HlgGanR75PeOIU5Tz0xqjCxtHA9OuU2ZH/oEuLQRpu', 'usuario', 'default.png', 1, '2026-01-06 14:44:33'),
(76, 'Maria Isabel Troncoso Sepulveda', '(+569)56848483', '16246873-1', 'mariatroncoso@dac.controls.com', '$2y$10$Wye1khi/Sq0HlgGanR75PeOIU5Tz0xqjCxtHA9OuU2ZH/oEuLQRpu', 'usuario', 'default.png', 1, '2026-01-06 14:44:33'),
(77, 'Francisco Javier Oyarzun Becerra', '(+569)37194280', '20390090-2', 'oyarzunf63@gmail.com', '$2y$10$Wye1khi/Sq0HlgGanR75PeOIU5Tz0xqjCxtHA9OuU2ZH/oEuLQRpu', 'usuario', 'default.png', 1, '2026-01-06 14:44:33'),
(78, 'Francisco Javier Salazar Toro', '(+569)51782441', '19497155-9', 'javiersalazartoro@gmail.com', '$2y$10$Wye1khi/Sq0HlgGanR75PeOIU5Tz0xqjCxtHA9OuU2ZH/oEuLQRpu', 'usuario', 'default.png', 1, '2026-01-06 14:44:33'),
(79, 'Fernando Javier González Moya', '(+569)44360290', '25672104-k', 'fernandoj.gonzalez@gmail.com', '$2y$10$Wye1khi/Sq0HlgGanR75PeOIU5Tz0xqjCxtHA9OuU2ZH/oEuLQRpu', 'usuario', 'default.png', 1, '2026-01-06 14:44:33'),
(80, 'Mateo Israel González Soto', '(+569)66694863', '21710754-7', 'mateognziz20@gmail.com', '$2y$10$Wye1khi/Sq0HlgGanR75PeOIU5Tz0xqjCxtHA9OuU2ZH/oEuLQRpu', 'usuario', 'default.png', 1, '2026-01-06 14:44:33'),
(81, 'Jose Miguel Cabeza Fuentealba', '(+569)91262518', '20904507-9', 'josecmndo8@gmail.com', '$2y$10$Wye1khi/Sq0HlgGanR75PeOIU5Tz0xqjCxtHA9OuU2ZH/oEuLQRpu', 'usuario', 'default.png', 1, '2026-01-06 14:44:33'),
(82, 'Rodrigo Andres Penroz Rojas', '(+569)79787518', '19972201-8', 'penrozrojasrodrigoandres@gmail.com', '$2y$10$Wye1khi/Sq0HlgGanR75PeOIU5Tz0xqjCxtHA9OuU2ZH/oEuLQRpu', 'usuario', 'default.png', 1, '2026-01-06 14:44:33'),
(83, 'Sabastian Ignacio Valderrama Ayala', '(+569)961820771', '22124539-3', 'sebastianvalderrama563@gmail.com', '$2y$10$Wye1khi/Sq0HlgGanR75PeOIU5Tz0xqjCxtHA9OuU2ZH/oEuLQRpu', 'usuario', 'default.png', 1, '2026-01-06 14:44:33');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `notas_tickets`
--
ALTER TABLE `notas_tickets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ticket_id` (`ticket_id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tareas`
--
ALTER TABLE `tareas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_asignado_id` (`usuario_asignado_id`),
  ADD KEY `creador_id` (`creador_id`);

--
-- Indices de la tabla `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `agente_id` (`agente_id`);

--
-- Indices de la tabla `ticket_checklist`
--
ALTER TABLE `ticket_checklist`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ticket_id` (`ticket_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `notas_tickets`
--
ALTER TABLE `notas_tickets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `tareas`
--
ALTER TABLE `tareas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT de la tabla `tickets`
--
ALTER TABLE `tickets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `ticket_checklist`
--
ALTER TABLE `ticket_checklist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `notas_tickets`
--
ALTER TABLE `notas_tickets`
  ADD CONSTRAINT `notas_tickets_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`),
  ADD CONSTRAINT `notas_tickets_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `tareas`
--
ALTER TABLE `tareas`
  ADD CONSTRAINT `tareas_ibfk_1` FOREIGN KEY (`usuario_asignado_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tareas_ibfk_2` FOREIGN KEY (`creador_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `tickets`
--
ALTER TABLE `tickets`
  ADD CONSTRAINT `tickets_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tickets_ibfk_3` FOREIGN KEY (`agente_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `ticket_checklist`
--
ALTER TABLE `ticket_checklist`
  ADD CONSTRAINT `ticket_checklist_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
