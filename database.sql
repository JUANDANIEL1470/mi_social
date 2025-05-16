-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 16-05-2025 a las 21:54:30
-- Versión del servidor: 9.1.0
-- Versión de PHP: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `mi_social`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `enlaces`
--

DROP TABLE IF EXISTS `enlaces`;
CREATE TABLE IF NOT EXISTS `enlaces` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL,
  `titulo` varchar(100) NOT NULL,
  `url` varchar(255) NOT NULL,
  `imagen` varchar(100) DEFAULT NULL,
  `orden` int DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `enlaces`
--

INSERT INTO `enlaces` (`id`, `usuario_id`, `titulo`, `url`, `imagen`, `orden`) VALUES
(8, 2, 'Pinterest', 'https://pinterest.com/prueba', 'pinterest.png', 1),
(7, 2, 'Instagram', 'https://instagram.com/prueba', 'instagram.png', 0),
(9, 2, 'Shopify', 'https://www.prueba.com/', 'shopify.png', 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `redes_sociales`
--

DROP TABLE IF EXISTS `redes_sociales`;
CREATE TABLE IF NOT EXISTS `redes_sociales` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `base_url` varchar(255) DEFAULT NULL,
  `imagen` varchar(100) NOT NULL,
  `placeholder` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `redes_sociales`
--

INSERT INTO `redes_sociales` (`id`, `nombre`, `base_url`, `imagen`, `placeholder`) VALUES
(1, 'Instagram', 'https://instagram.com/', 'instagram.png', 'tu_usuario'),
(2, 'Facebook', 'https://facebook.com/', 'facebook.png', 'tu_usuario'),
(3, 'Twitter', 'https://twitter.com/', 'twitter.png', 'tu_usuario'),
(4, 'TikTok', 'https://tiktok.com/@', 'tik-tok.png', 'tu_usuario'),
(5, 'YouTube', 'https://youtube.com/', 'youtube.png', 'tu_canal'),
(6, 'LinkedIn', 'https://linkedin.com/in/', 'linkedin.png', 'tu_perfil'),
(7, 'WhatsApp', 'https://wa.me/', 'whatsapp.png', 'tu_numero'),
(8, 'Telegram', 'https://t.me/', 'telegram.png', 'tu_usuario'),
(9, 'Pinterest', 'https://pinterest.com/', 'pinterest.png', 'tu_usuario'),
(10, 'Reddit', 'https://reddit.com/user/', 'reddit.png', 'tu_usuario'),
(11, 'Twitch', 'https://twitch.tv/', 'twitch.png', 'tu_canal'),
(12, 'Discord', NULL, 'discord.png', 'tu_invitación'),
(13, 'Spotify', 'https://open.spotify.com/user/', 'spotify.png', 'tu_usuario'),
(14, 'Snapchat', 'https://snapchat.com/add/', 'snapchat.png', 'tu_usuario'),
(15, 'GitHub', 'https://github.com/', 'github.png', 'tu_usuario'),
(16, 'Messenger', 'https://m.me/', 'messenger.png', 'tu_usuario'),
(17, 'Shopify', NULL, 'shopify.png', 'tu_tienda'),
(18, 'PayPal', NULL, 'paypal.png', 'tu_enlace'),
(19, 'Skype', NULL, 'skype.png', 'tu_usuario'),
(20, 'Vimeo', 'https://vimeo.com/', 'vimeo.png', 'tu_canal'),
(21, 'VK', 'https://vk.com/', 'vk.png', 'tu_perfil'),
(22, 'Clubhouse', NULL, 'clubhouse.png', 'tu_sala'),
(23, 'Threads', 'https://threads.net/@', 'threads.png', 'tu_usuario'),
(24, 'Patreon', 'https://patreon.com/', 'patreon.png', 'tu_página'),
(25, 'Blogger', NULL, 'blogger.png', 'tu_blog'),
(26, 'Caffeine', NULL, 'caffeine.png', 'tu_canal'),
(27, 'Line', NULL, 'line.png', 'tu_usuario'),
(28, 'Xing', NULL, 'xing.png', 'tu_perfil');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nombre_perfil` varchar(100) DEFAULT NULL,
  `bio` text,
  `avatar` varchar(255) DEFAULT NULL,
  `tema_color` varchar(50) DEFAULT '#3498db',
  `fecha_registro` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `remember_token` varchar(100) DEFAULT NULL,
  `token_expiry` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `username`, `email`, `password`, `nombre_perfil`, `bio`, `avatar`, `tema_color`, `fecha_registro`, `remember_token`, `token_expiry`) VALUES
(2, 'admin', 'admin@admin.com', '$2y$10$SlcBd4vcKFw6Xf6Tl5OsD.7dIUaLE71JgQUmnUt4FDj/MTZ8rv3Cu', 'PRUEBA', 'PRUEBA AAAAA SSS AASSS AA AAS 1 21| 2 123 2Ñ 123ÑÑ 1Ñ3Ñ12 L3. . ´12 {SDA+{AÑ.S.D .Ñ .´4250I48U21 JI NS DA', 'assets/uploads/avatar_2_1747432094.png', '#ff0000', '2025-05-15 18:20:00', 'dc4f237416c55abca4235e11d8e6e9b9f90802cc1cb620d2fef3d24ff577c747', '2025-06-15 21:45:29');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `visitas`
--

DROP TABLE IF EXISTS `visitas`;
CREATE TABLE IF NOT EXISTS `visitas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `enlace_id` int NOT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `fecha` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `enlace_id` (`enlace_id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `visitas`
--

INSERT INTO `visitas` (`id`, `enlace_id`, `ip`, `fecha`) VALUES
(1, 6, '::1', '2025-05-15 21:41:19'),
(2, 6, '::1', '2025-05-15 21:41:20'),
(3, 4, '::1', '2025-05-15 21:41:26'),
(4, 4, '::1', '2025-05-15 21:41:27'),
(5, 4, '::1', '2025-05-15 21:41:33'),
(6, 4, '::1', '2025-05-15 21:41:34'),
(7, 4, '::1', '2025-05-16 19:18:47'),
(8, 4, '::1', '2025-05-16 19:18:48');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
