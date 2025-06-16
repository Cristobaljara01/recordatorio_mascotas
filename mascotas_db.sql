-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 16-06-2025 a las 17:32:41
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `mascotas_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `agregar_vacuna`
--

CREATE TABLE `agregar_vacuna` (
  `id` int(11) NOT NULL,
  `mascota_id` int(11) NOT NULL,
  `nombre_vacuna` varchar(100) NOT NULL,
  `fecha_aplicacion` date NOT NULL,
  `proxima_dosis` date DEFAULT NULL,
  `observaciones` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `agregar_vacuna`
--

INSERT INTO `agregar_vacuna` (`id`, `mascota_id`, `nombre_vacuna`, `fecha_aplicacion`, `proxima_dosis`, `observaciones`) VALUES
(1, 4, 'hh', '2025-06-08', '2025-06-19', 'u'),
(2, 4, 'ed3rfderf', '2025-06-04', '2025-06-13', 'aa'),
(3, 4, 'n jvbjb', '2025-06-09', '2025-06-30', 'fxgfhfd'),
(4, 4, 'uy', '2025-06-16', '2025-06-25', 'kjgoiu'),
(5, 4, 'rabia', '2025-06-16', '2025-06-30', 'se enojo'),
(6, 4, 'rabia', '2025-06-10', '2025-06-30', 'aaaaaaaaaa'),
(7, 3, 'fghfhj', '2025-06-11', '2025-06-25', 'vía nasal');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `editar_vacuna`
--

CREATE TABLE `editar_vacuna` (
  `nombre_de_la_vacuna` int(11) NOT NULL,
  `fecha_aplicacion` date NOT NULL,
  `proxima_dosis` date NOT NULL,
  `obsevaciones` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mascotas`
--

CREATE TABLE `mascotas` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `especie` varchar(50) NOT NULL,
  `raza` varchar(100) NOT NULL,
  `fecha_nac` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `mascotas`
--

INSERT INTO `mascotas` (`id`, `nombre`, `especie`, `raza`, `fecha_nac`) VALUES
(3, 'alejandro', 'kiltro chileno', 'jano', '2025-05-08'),
(4, 'fuck', 'perro', 'golden', '2023-03-13');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vacunas`
--

CREATE TABLE `vacunas` (
  `id` int(11) NOT NULL,
  `id_mascota` int(11) NOT NULL,
  `nombre_vacunas` varchar(100) NOT NULL,
  `fecha_aplicacion` date NOT NULL,
  `proxima_dosis` date NOT NULL,
  `observaciones` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `agregar_vacuna`
--
ALTER TABLE `agregar_vacuna`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `editar_vacuna`
--
ALTER TABLE `editar_vacuna`
  ADD PRIMARY KEY (`nombre_de_la_vacuna`);

--
-- Indices de la tabla `mascotas`
--
ALTER TABLE `mascotas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `vacunas`
--
ALTER TABLE `vacunas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_mascota` (`id_mascota`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `agregar_vacuna`
--
ALTER TABLE `agregar_vacuna`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `mascotas`
--
ALTER TABLE `mascotas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `vacunas`
--
ALTER TABLE `vacunas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
