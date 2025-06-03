-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 03. Jun 2025 um 17:45
-- Server-Version: 10.4.32-MariaDB
-- PHP-Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `projekt_datenbank`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `imported_files`
--

CREATE TABLE `imported_files` (
  `id` int(11) NOT NULL,
  `file_hash` varchar(64) NOT NULL,
  `imported_at` datetime DEFAULT current_timestamp(),
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `imported_files`
--

INSERT INTO `imported_files` (`id`, `file_hash`, `imported_at`, `user_id`) VALUES
(13, '19dfd7e962f51b5f98aa20a65947d1ec', '2025-06-03 11:43:18', 8),
(14, 'c07dd29974292d036e1359880d6b7172', '2025-06-03 13:49:34', 14);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `temperature`
--

CREATE TABLE `temperature` (
  `id` int(11) NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `temperature` int(11) NOT NULL,
  `humidity` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `temperature`
--

INSERT INTO `temperature` (`id`, `date`, `time`, `temperature`, `humidity`, `user_id`) VALUES
(0, '2025-06-02', '20:00:00', 17, 30, 8),
(0, '2025-06-02', '21:00:00', 16, 30, 8),
(0, '2025-06-02', '22:00:00', 15, 20, 8),
(0, '2025-06-02', '23:00:00', 14, 20, 8),
(0, '2025-06-02', '00:00:00', 13, 20, 8),
(0, '2025-06-03', '01:00:00', 9, 0, 8),
(0, '2025-06-03', '02:00:00', 10, 0, 8),
(0, '2025-06-03', '03:00:00', 10, 5, 8),
(0, '2025-06-03', '04:00:00', 10, 10, 8),
(0, '2025-06-03', '05:00:00', 11, 20, 8),
(0, '2025-06-02', '20:00:00', 17, 30, 14),
(0, '2025-06-02', '21:00:00', 16, 30, 14),
(0, '2025-06-02', '22:00:00', 15, 20, 14),
(0, '2025-06-02', '23:00:00', 14, 20, 14),
(0, '2025-06-03', '01:00:00', 9, 0, 14),
(0, '2025-06-03', '02:00:00', 10, 0, 14),
(0, '2025-06-03', '03:00:00', 10, 5, 14),
(0, '2025-06-03', '04:00:00', 10, 10, 14),
(0, '2025-06-03', '05:00:00', 11, 20, 14);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `user_name` varchar(128) NOT NULL,
  `password` varchar(255) NOT NULL,
  `is_admin` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `users`
--

INSERT INTO `users` (`id`, `user_name`, `password`, `is_admin`) VALUES
(14, 'Admin', '$2y$10$OqU66xZ9S/0FgQ.wDAMxhO6GuPG6jTj0UwVI6LTlEmgAk5NX.rAky', 1),
(17, 'test', '$2y$10$Yb.Xs8gd5gkrYYB87dBt9uYmh1a2/sARh61IzlszSiZJzmEKBnLV2', 0),
(18, 'Elias Mieth', '$2y$10$p5IVQ3UMbCkestX9IeH0Le0ahmMc6O95CxhQelyoapyOAF7D7jNrG', 0);

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `imported_files`
--
ALTER TABLE `imported_files`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `file_hash` (`file_hash`);

--
-- Indizes für die Tabelle `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `imported_files`
--
ALTER TABLE `imported_files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT für Tabelle `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
