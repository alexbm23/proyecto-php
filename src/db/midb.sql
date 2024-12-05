-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: db
-- Tiempo de generación: 02-12-2024 a las 12:31:45
-- Versión del servidor: 11.5.2-MariaDB-ubu2404
-- Versión de PHP: 8.2.23

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `midb`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `api_version`
--

CREATE TABLE `api_version` (
  `id` int(11) NOT NULL,
  `version` varchar(50) NOT NULL,
  `last_update` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Volcado de datos para la tabla `api_version`
--

INSERT INTO `api_version` (`id`, `version`, `last_update`) VALUES
(1, '14.23.1', '2024-11-28 17:30:34');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `champions`
--

CREATE TABLE `champions` (
  `id` varchar(150) NOT NULL,
  `key` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Volcado de datos para la tabla `champions`
--

INSERT INTO `champions` (`id`, `key`) VALUES
('Aatrox', 266),
('Ahri', 103),
('Akali', 84),
('Akshan', 166),
('Alistar', 12),
('Ambessa', 799),
('Amumu', 32),
('Anivia', 34),
('Annie', 1),
('Aphelios', 523),
('Ashe', 22),
('AurelionSol', 136),
('Aurora', 893),
('Azir', 268),
('Bard', 432),
('Belveth', 200),
('Blitzcrank', 53),
('Brand', 63),
('Braum', 201),
('Briar', 233),
('Caitlyn', 51),
('Camille', 164),
('Cassiopeia', 69),
('Chogath', 31),
('Corki', 42),
('Darius', 122),
('Diana', 131),
('Draven', 119),
('DrMundo', 36),
('Ekko', 245),
('Elise', 60),
('Evelynn', 28),
('Ezreal', 81),
('Fiddlesticks', 9),
('Fiora', 114),
('Fizz', 105),
('Galio', 3),
('Gangplank', 41),
('Garen', 86),
('Gnar', 150),
('Gragas', 79),
('Graves', 104),
('Gwen', 887),
('Hecarim', 120),
('Heimerdinger', 74),
('Hwei', 910),
('Illaoi', 420),
('Irelia', 39),
('Ivern', 427),
('Janna', 40),
('JarvanIV', 59),
('Jax', 24),
('Jayce', 126),
('Jhin', 202),
('Jinx', 222),
('Kaisa', 145),
('Kalista', 429),
('Karma', 43),
('Karthus', 30),
('Kassadin', 38),
('Katarina', 55),
('Kayle', 10),
('Kayn', 141),
('Kennen', 85),
('Khazix', 121),
('Kindred', 203),
('Kled', 240),
('KogMaw', 96),
('KSante', 897),
('Leblanc', 7),
('LeeSin', 64),
('Leona', 89),
('Lillia', 876),
('Lissandra', 127),
('Lucian', 236),
('Lulu', 117),
('Lux', 99),
('Malphite', 54),
('Malzahar', 90),
('Maokai', 57),
('MasterYi', 11),
('Milio', 902),
('MissFortune', 21),
('MonkeyKing', 62),
('Mordekaiser', 82),
('Morgana', 25),
('Naafiri', 950),
('Nami', 267),
('Nasus', 75),
('Nautilus', 111),
('Neeko', 518),
('Nidalee', 76),
('Nilah', 895),
('Nocturne', 56),
('Nunu', 20),
('Olaf', 2),
('Orianna', 61),
('Ornn', 516),
('Pantheon', 80),
('Poppy', 78),
('Pyke', 555),
('Qiyana', 246),
('Quinn', 133),
('Rakan', 497),
('Rammus', 33),
('RekSai', 421),
('Rell', 526),
('Renata', 888),
('Renekton', 58),
('Rengar', 107),
('Riven', 92),
('Rumble', 68),
('Ryze', 13),
('Samira', 360),
('Sejuani', 113),
('Senna', 235),
('Seraphine', 147),
('Sett', 875),
('Shaco', 35),
('Shen', 98),
('Shyvana', 102),
('Singed', 27),
('Sion', 14),
('Sivir', 15),
('Skarner', 72),
('Smolder', 901),
('Sona', 37),
('Soraka', 16),
('Swain', 50),
('Sylas', 517),
('Syndra', 134),
('TahmKench', 223),
('Taliyah', 163),
('Talon', 91),
('Taric', 44),
('Teemo', 17),
('Thresh', 412),
('Tristana', 18),
('Trundle', 48),
('Tryndamere', 23),
('TwistedFate', 4),
('Twitch', 29),
('Udyr', 77),
('Urgot', 6),
('Varus', 110),
('Vayne', 67),
('Veigar', 45),
('Velkoz', 161),
('Vex', 711),
('Vi', 254),
('Viego', 234),
('Viktor', 112),
('Vladimir', 8),
('Volibear', 106),
('Warwick', 19),
('Xayah', 498),
('Xerath', 101),
('XinZhao', 5),
('Yasuo', 157),
('Yone', 777),
('Yorick', 83),
('Yuumi', 350),
('Zac', 154),
('Zed', 238),
('Zeri', 221),
('Ziggs', 115),
('Zilean', 26),
('Zoe', 142),
('Zyra', 143);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `api_version`
--
ALTER TABLE `api_version`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `champions`
--
ALTER TABLE `champions`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `api_version`
--
ALTER TABLE `api_version`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
