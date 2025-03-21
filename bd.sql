-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 04-12-2024 a las 20:31:00
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `bd`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `analistas`
--

CREATE TABLE `analistas` (
  `id_analista` int(11) NOT NULL,
  `nombre_analista` varchar(100) NOT NULL,
  `ap_analista` varchar(100) NOT NULL,
  `am_analista` varchar(100) NOT NULL,
  `usuario_analista` varchar(100) NOT NULL,
  `contrasenia_analista` varchar(100) NOT NULL,
  `rol` int(11) NOT NULL,
  `estado_analista` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `analistas`
--

INSERT INTO `analistas` (`id_analista`, `nombre_analista`, `ap_analista`, `am_analista`, `usuario_analista`, `contrasenia_analista`, `rol`, `estado_analista`) VALUES
(0, 'Nulo', 'Nulo', 'Nulo', 'Nulo', 'Nulo', 1, 1),
(1, 'Alex ', 'El', 'Leon', 'AlexLeon', '12345', 1, 1),
(2, 'Elisa', 'Elisa', 'Angel', 'Elisa', '12345', 2, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `consultapersonas`
--

CREATE TABLE `consultapersonas` (
  `id_consulta` int(11) NOT NULL,
  `id_oficial` int(11) NOT NULL,
  `referenciaS` int(11) NOT NULL,
  `motivo_consulta` varchar(100) NOT NULL,
  `nombre_sospechoso` varchar(100) NOT NULL,
  `ap_sospechoso` varchar(100) NOT NULL,
  `am_sospechoso` varchar(100) NOT NULL,
  `fechaNacimiento_sospechoso` varchar(20) NOT NULL,
  `estado` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `consultapersonas`
--

INSERT INTO `consultapersonas` (`id_consulta`, `id_oficial`, `referenciaS`, `motivo_consulta`, `nombre_sospechoso`, `ap_sospechoso`, `am_sospechoso`, `fechaNacimiento_sospechoso`, `estado`) VALUES
(1, 1, 1, 'Se veia sospechoso', 'Angel', 'Salcedo', 'Hurtado', '10/12/2002', 3),
(2, 1, 2, 'Caminar sospechoso', 'Eduardo', 'Lopez', 'Estrada', '20/10/2000', 3),
(3, 1, 3, 'Tomar en via publica', 'Laura', 'Lopez', 'Limon', '30/08/1998', 3),
(4, 1, 6, 'se veía raro', 'angel', 'perez', 'hurtado', '10/10/2002', 3),
(5, 1, 8, 'ejemplo', 'jorge', 'lopez', 'mateos', '08/10/2002', 3),
(6, 1, 33, 'prueba', 'prueba', 'prueba', 'prueba', '20/10/2002', 4),
(7, 1, 35, 'prueba', 'prueba', 'prueba', 'prueba', '20/10/2002', 3),
(8, 1, 36, 'prueba', 'prueba', 'prueba', 'prueba', '20/10/2002', 3),
(9, 1, 37, 'prueba', 'prueba', 'prueba', 'prueba', '20/10/2002', 3),
(10, 1, 41, 'prueba', 'prueba', 'prueba', 'prueba', '20/10/2002', 3),
(11, 1, 42, 'prueba', 'prueba', 'prueba', 'prueba', '20/10/2002', 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `consultavehiculos`
--

CREATE TABLE `consultavehiculos` (
  `referenciaV` int(11) NOT NULL,
  `id_oficial` int(11) NOT NULL,
  `referenciaS` int(11) NOT NULL,
  `motivo_consulta` varchar(110) NOT NULL,
  `no_serie` varchar(110) NOT NULL,
  `placa` varchar(110) NOT NULL,
  `nom_sospechoso` varchar(100) NOT NULL,
  `estado` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `consultavehiculos`
--

INSERT INTO `consultavehiculos` (`referenciaV`, `id_oficial`, `referenciaS`, `motivo_consulta`, `no_serie`, `placa`, `nom_sospechoso`, `estado`) VALUES
(1, 1, 7, 'venia tomado', '598428', 'pra-46-42', 'angel salcedo hurtado', 3),
(2, 1, 9, 'sospechoso', '459685', 'asv-46-85', 'enrique lopez lopez', 3),
(3, 1, 10, 'sospechoso', '459685', 'asv-46-85', 'enrique lopez flores', 3),
(4, 1, 11, 'prueba', 'prueba', 'prueba', 'prueba', 3),
(5, 1, 12, 'prueba', 'a', 'a', 'a', 3),
(6, 1, 13, 'a', 'a', 'a', 'a', 3),
(7, 1, 14, 'a', 'a', 'a', 'a', 3),
(8, 1, 15, 'a', 'a', 'a', 'a', 3),
(9, 1, 16, 'a', 'a', 'a', 'a', 5),
(10, 1, 17, 'a', 'a', 'a', 'a', 3),
(11, 1, 18, 'a', 'a', 'a', 'a', 5),
(12, 1, 19, 'a', 'a', 'a', 'a', 3),
(13, 1, 20, 'a', 'a', 'a', 'a', 3),
(14, 1, 21, 'a', 'a', 'a', 'a', 3),
(15, 1, 22, 'prueba', 'prueba', 'prueba', 'prueba', 5),
(16, 1, 23, 'prueba', 'prueba', 'prueba', 'prueba', 3),
(17, 1, 24, 'sospechos', '654651', 'ejemplo', 'ejemplo ruiz ejemplo', 3),
(18, 1, 25, 'prueba', 'prueba', 'prueba', 'assad', 3),
(19, 1, 26, 'prueba', 'prueba', 'prueba', 'prueba', 5),
(20, 1, 27, 'prueba', 'prueba', 'prueba', 'prueba', 3),
(21, 1, 28, 'prueba', 'prueba', 'prueba', 'prueba', 3),
(22, 1, 29, 'prueba', 'prueba', 'prueba', 'prueba', 3),
(23, 1, 30, 'prueba', 'prueba', 'prueba', 'prueba', 3),
(24, 1, 31, 'prueba', 'prueba', 'prueba', 'prueba', 5),
(25, 1, 32, 'prueba', 'prueba', 'prueba', 'prueba', 5),
(26, 1, 34, 'prueba', 'prueba', 'prueba', 'prueba', 3),
(27, 1, 38, 'prueba', 'prueba', 'prueba', 'prueba1', 3),
(28, 1, 39, 'prueba', 'prueba', 'prueba', 'prueba', 3),
(29, 1, 40, 'prueba', 'prueba', 'prueba', 'prueba', 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `hitsoficiales`
--

CREATE TABLE `hitsoficiales` (
  `id_hit` int(11) NOT NULL,
  `id_oficial` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `num_hits` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `oficiales`
--

CREATE TABLE `oficiales` (
  `id_oficial` int(11) NOT NULL,
  `nombre_oficial` varchar(100) NOT NULL,
  `ap_oficial` varchar(100) NOT NULL,
  `am_oficial` varchar(100) NOT NULL,
  `telefono_oficial` varchar(100) NOT NULL,
  `nomina_oficial` varchar(100) NOT NULL,
  `unidad_oficial` varchar(100) NOT NULL,
  `verificado_oficial` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `oficiales`
--

INSERT INTO `oficiales` (`id_oficial`, `nombre_oficial`, `ap_oficial`, `am_oficial`, `telefono_oficial`, `nomina_oficial`, `unidad_oficial`, `verificado_oficial`) VALUES
(1, 'Angel ', 'Salcedo', 'Hurtado', '4444173656', 'A123456', '45', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `seguimientopersonas`
--

CREATE TABLE `seguimientopersonas` (
  `referenciaP` int(11) NOT NULL,
  `id_oficial` int(11) NOT NULL,
  `fecha` varchar(20) NOT NULL,
  `ubicacion` varchar(100) NOT NULL,
  `colonia` varchar(100) NOT NULL,
  `sector` varchar(100) NOT NULL,
  `edad_detenido` int(11) NOT NULL,
  `nacionalidad_detenido` varchar(100) NOT NULL,
  `domicilio_detenido` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `seguimientopersonas`
--

INSERT INTO `seguimientopersonas` (`referenciaP`, `id_oficial`, `fecha`, `ubicacion`, `colonia`, `sector`, `edad_detenido`, `nacionalidad_detenido`, `domicilio_detenido`) VALUES
(37, 1, '2024-12-04', 'prueba', 'prueba', 'norte', 0, 'prueba', 'prueba1'),
(42, 1, '2024-12-04', 'prueba', 'prueba', 'norte', 0, 'prueba', 'prueba1');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `seguimientovehiculos`
--

CREATE TABLE `seguimientovehiculos` (
  `referenciaV` int(11) NOT NULL,
  `id_oficial` int(11) NOT NULL,
  `fecha` varchar(20) NOT NULL,
  `ubicacion` varchar(100) NOT NULL,
  `colonia` varchar(100) NOT NULL,
  `sector` varchar(100) NOT NULL,
  `caracteristicasV` varchar(100) NOT NULL,
  `condicionesV` varchar(100) NOT NULL,
  `nombre_conductor` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `seguimientovehiculos`
--

INSERT INTO `seguimientovehiculos` (`referenciaV`, `id_oficial`, `fecha`, `ubicacion`, `colonia`, `sector`, `caracteristicasV`, `condicionesV`, `nombre_conductor`) VALUES
(32, 1, '2024-12-04', 'prueba', 'prueba', 'prueba', 'prueba', 'prueba', 'prueba');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `solicitudes`
--

CREATE TABLE `solicitudes` (
  `referenciaS` int(11) NOT NULL,
  `mensaje` varchar(100) NOT NULL,
  `telefono_oficial` varchar(100) NOT NULL,
  `estado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `solicitudes`
--

INSERT INTO `solicitudes` (`referenciaS`, `mensaje`, `telefono_oficial`, `estado`) VALUES
(1, '', '4444173656', 3),
(2, '', '4444173656', 3),
(3, '', '4444173656', 3),
(4, '', '4444173656', 4),
(5, '', '4444173656', 4),
(6, '', '4444173656', 3),
(7, '', '4444173656', 3),
(8, '', '4444173656', 3),
(9, '', '4444173656', 3),
(10, '', '4444173656', 3),
(11, '', '4444173656', 3),
(12, '', '4444173656', 3),
(13, '', '4444173656', 3),
(14, 'Prosiga', '4444173656', 3),
(15, 'Prosiga', '4444173656', 3),
(16, 'No se encontró información', '4444173656', 3),
(17, 'No se encontró información', '4444173656', 3),
(18, 'Prosiga', '4444173656', 3),
(19, 'No se encontró información', '4444173656', 3),
(20, 'No se encontró información', '4444173656', 3),
(21, 'No se encontró información', '4444173656', 3),
(22, 'No se encontró información', '4444173656', 3),
(23, 'Prosiga', '4444173656', 3),
(24, 'Prosiga', '4444173656', 3),
(25, 'Prosiga', '4444173656', 3),
(26, 'No se encontró información', '4444173656', 3),
(27, 'Prosiga', '4444173656', 3),
(28, 'No se encontró información', '4444173656', 3),
(29, 'No se encontró información', '4444173656', 3),
(30, 'Prosiga', '4444173656', 3),
(31, 'No se encontró información', '4444173656', 3),
(32, 'No se encontró información', '4444173656', 3),
(33, '', '4444173656', 4),
(34, 'Prosiga', '4444173656', 3),
(35, '', '4444173656', 3),
(36, 'Prosiga', '4444173656', 3),
(37, 'No se encontró información', '4444173656', 3),
(38, 'Prosiga', '4444173656', 3),
(39, 'No se encontró información', '4444173656', 3),
(40, 'asdas', '4444173656', 3),
(41, 'No se encontró información', '4444173656', 3),
(42, 'No se encontró información', '4444173656', 3);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `analistas`
--
ALTER TABLE `analistas`
  ADD PRIMARY KEY (`id_analista`);

--
-- Indices de la tabla `consultapersonas`
--
ALTER TABLE `consultapersonas`
  ADD PRIMARY KEY (`id_consulta`),
  ADD KEY `referenciaS` (`referenciaS`);

--
-- Indices de la tabla `consultavehiculos`
--
ALTER TABLE `consultavehiculos`
  ADD PRIMARY KEY (`referenciaV`),
  ADD KEY `referenciaS` (`referenciaS`),
  ADD KEY `id_oficial` (`id_oficial`);

--
-- Indices de la tabla `hitsoficiales`
--
ALTER TABLE `hitsoficiales`
  ADD PRIMARY KEY (`id_hit`),
  ADD KEY `id_oficial` (`id_oficial`);

--
-- Indices de la tabla `oficiales`
--
ALTER TABLE `oficiales`
  ADD PRIMARY KEY (`id_oficial`);

--
-- Indices de la tabla `seguimientopersonas`
--
ALTER TABLE `seguimientopersonas`
  ADD KEY `id_oficial` (`id_oficial`);

--
-- Indices de la tabla `seguimientovehiculos`
--
ALTER TABLE `seguimientovehiculos`
  ADD KEY `referenciaV` (`id_oficial`);

--
-- Indices de la tabla `solicitudes`
--
ALTER TABLE `solicitudes`
  ADD PRIMARY KEY (`referenciaS`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `analistas`
--
ALTER TABLE `analistas`
  MODIFY `id_analista` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `consultapersonas`
--
ALTER TABLE `consultapersonas`
  MODIFY `id_consulta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `consultavehiculos`
--
ALTER TABLE `consultavehiculos`
  MODIFY `referenciaV` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT de la tabla `hitsoficiales`
--
ALTER TABLE `hitsoficiales`
  MODIFY `id_hit` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `oficiales`
--
ALTER TABLE `oficiales`
  MODIFY `id_oficial` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `solicitudes`
--
ALTER TABLE `solicitudes`
  MODIFY `referenciaS` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `consultapersonas`
--
ALTER TABLE `consultapersonas`
  ADD CONSTRAINT `consultapersonas_ibfk_1` FOREIGN KEY (`referenciaS`) REFERENCES `solicitudes` (`referenciaS`),
  ADD CONSTRAINT `consultapersonas_ibfk_2` FOREIGN KEY (`id_oficial`) REFERENCES `oficiales` (`id_oficial`);

--
-- Filtros para la tabla `consultavehiculos`
--
ALTER TABLE `consultavehiculos`
  ADD CONSTRAINT `consultavehiculos_ibfk_1` FOREIGN KEY (`referenciaS`) REFERENCES `solicitudes` (`referenciaS`);

--
-- Filtros para la tabla `hitsoficiales`
--
ALTER TABLE `hitsoficiales`
  ADD CONSTRAINT `hitsoficiales_ibfk_1` FOREIGN KEY (`id_oficial`) REFERENCES `oficiales` (`id_oficial`);

--
-- Filtros para la tabla `seguimientopersonas`
--
ALTER TABLE `seguimientopersonas`
  ADD CONSTRAINT `seguimientopersonas_ibfk_1` FOREIGN KEY (`id_oficial`) REFERENCES `oficiales` (`id_oficial`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
