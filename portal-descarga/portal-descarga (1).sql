-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 21-06-2024 a las 14:31:15
-- Versión del servidor: 10.4.28-MariaDB
-- Versión de PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `portal-descarga`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_customer`
--

CREATE TABLE `tbl_customer` (
  `tbl_customer_id` int(11) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `contact_number` varchar(20) NOT NULL,
  `email` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `verification_code` int(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tbl_customer`
--

INSERT INTO `tbl_customer` (`tbl_customer_id`, `first_name`, `last_name`, `contact_number`, `email`, `username`, `password`, `verification_code`) VALUES
(2, 'Juan', 'Cliente', '3025861423', 'msevillab@gmail.com', 'jmanuel', '$2y$10$vkZV9JkXguF.sOjYWxvB.eon0PFETeL6ROEoFHQMUEInyNlBejCj.', 850034),
(9, 'Mauricio', 'Sevilla', '3042702375', 'configuroweb@demoscweb.com', 'configuroweb', '$2y$10$8OetgCh2wxdrHv1QeFr6YeYbLEEzou1VMs3qB26xbgZXg/c/7ftNm', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_file`
--

CREATE TABLE `tbl_file` (
  `tbl_file_id` int(11) NOT NULL,
  `file_title` varchar(255) NOT NULL,
  `file` varchar(255) NOT NULL,
  `file_uploader` varchar(255) NOT NULL,
  `date_uploaded` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` enum('pending','approved') DEFAULT 'pending',
  `pre_published` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tbl_file`
--

INSERT INTO `tbl_file` (`tbl_file_id`, `file_title`, `file`, `file_uploader`, `date_uploaded`, `status`, `pre_published`) VALUES
(13, 'Archivo 1', 'Archivo 3.docx', 'jusuario', '2024-06-06 02:06:33', 'approved', 0),
(14, 'Libro PDF 1', 'Libro pdf.pdf', 'jusuario', '2024-06-06 02:04:44', 'approved', 0),
(15, 'Libro PDF 2', 'Libro2 pdf.pdf', 'configuroweb', '2024-06-06 02:03:56', 'approved', 0),
(16, 'Libro PDF 3', 'Libro3 pdf.pdf', 'dusuario', '2024-06-06 02:31:43', 'approved', 1),
(17, 'Documento 4 Word', 'Documento 4 Word.docx', 'configuroweb', '2024-06-06 15:06:11', 'pending', 0),
(22, 'Sistema de Inicio de Sesión con Verificación de Correo Electrónico', 'Sistema de Inicio de Sesión con Verificación de Correo Electrónico (1) (1).rar', 'configuroweb', '2024-06-20 08:19:56', 'approved', 1),
(24, 'Portal de Archivos PDF en PHP y MySQL', 'Portal de Archivos PDF en PHP y MySQL.rar', 'configuroweb', '2024-06-20 08:40:19', 'approved', 1),
(25, 'Final PDF', 'Libro PDF 4.pdf', 'configuroweb', '2024-06-20 08:42:11', 'pending', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_product`
--

CREATE TABLE `tbl_product` (
  `product_id` int(11) NOT NULL,
  `file_id` int(11) NOT NULL,
  `description` text NOT NULL,
  `image` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tbl_product`
--

INSERT INTO `tbl_product` (`product_id`, `file_id`, `description`, `image`, `price`) VALUES
(1, 22, 'Este Sistema de Inicio de Sesión con Verificación de Correo Electrónico, permite a los usuarios sencillamente registrarse en la aplicación, actualizar su información, eliminar y visualizar los usuarios registrados.', 'Sistema de Inicio de Sesión con Verificación de Correo Electrónico.jpg', 15.00),
(2, 24, 'Este sistema Portal de Archivos PDF en PHP y MySQL le permite a los usuarios de perfil usuario y administrador, subir archivos PDF para que estos se puedan publicar en una página de libre acceso.', 'Portal de Archivos PDF en PHP y MySQL.jpg', 15.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_users`
--

CREATE TABLE `tbl_users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(100) NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `tbl_users`
--

INSERT INTO `tbl_users` (`user_id`, `username`, `password`, `role`) VALUES
(1, 'configuroweb', '$2y$10$nPzhch9M8gDOxWOZYbA7BOwQ8sLq7Vv9JN/oIzmki0pjRCWiIcpd6', 'admin'),
(2, 'jusuario', '$2y$10$KUI7J9JW85Bd8u/c5g5bT.lreyu6xPanHqQ9ozdFOyjk3sOHUHlFC', 'user'),
(8, 'jadministrador', '$2y$10$qnAeEHDXShZ62EKYhYR5Buer9W32nCcouEcSwOFnhDQdRVevsOl1m', 'admin'),
(11, 'dusuario', '$2y$10$CHf5n.582cj31MtejDs20OxbNN3pbDobhrAIa91Nypi6Jt8ifxPg6', 'user');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `tbl_customer`
--
ALTER TABLE `tbl_customer`
  ADD PRIMARY KEY (`tbl_customer_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indices de la tabla `tbl_file`
--
ALTER TABLE `tbl_file`
  ADD PRIMARY KEY (`tbl_file_id`);

--
-- Indices de la tabla `tbl_product`
--
ALTER TABLE `tbl_product`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `file_id` (`file_id`);

--
-- Indices de la tabla `tbl_users`
--
ALTER TABLE `tbl_users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `tbl_customer`
--
ALTER TABLE `tbl_customer`
  MODIFY `tbl_customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `tbl_file`
--
ALTER TABLE `tbl_file`
  MODIFY `tbl_file_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de la tabla `tbl_product`
--
ALTER TABLE `tbl_product`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `tbl_users`
--
ALTER TABLE `tbl_users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `tbl_product`
--
ALTER TABLE `tbl_product`
  ADD CONSTRAINT `fk_file_product` FOREIGN KEY (`file_id`) REFERENCES `tbl_file` (`tbl_file_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
