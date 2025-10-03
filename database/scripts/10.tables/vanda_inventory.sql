-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Gegenereerd op: 25 sep 2025 om 13:10
-- Serverversie: 10.6.22-MariaDB-0ubuntu0.22.04.1
-- PHP-versie: 8.3.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `vanda`
--

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `vanda_inventory`
--

CREATE TABLE `vanda_inventory` (
  `id` int(11) NOT NULL,
  `barcode` varchar(250) NOT NULL,
  `quality` varchar(250) NOT NULL,
  `lengte` DECIMAL(10,2) NULL,
  `breedte` DECIMAL(10,2) NULL,
  `location` varchar(250) NOT NULL,
  `processed` tinyint(1) NOT NULL,
  `date` datetime NOT NULL,
  `modified` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexen voor geëxporteerde tabellen
--

--
-- Indexen voor tabel `vanda_inventory`
--
ALTER TABLE `vanda_inventory`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT voor geëxporteerde tabellen
--

--
-- AUTO_INCREMENT voor een tabel `vanda_inventory`
--
ALTER TABLE `vanda_inventory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
