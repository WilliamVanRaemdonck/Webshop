-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Gegenereerd op: 22 dec 2022 om 20:28
-- Serverversie: 10.4.24-MariaDB
-- PHP-versie: 8.1.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `webshop`
--

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `orders`
--

CREATE TABLE `orders` (
  `OrderID` int(11) NOT NULL,
  `userNumber` int(11) NOT NULL,
  `Datum` date NOT NULL,
  `Betaald` int(11) NOT NULL,
  `totaalPrijs` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Gegevens worden geëxporteerd voor tabel `orders`
--

INSERT INTO `orders` (`OrderID`, `userNumber`, `Datum`, `Betaald`, `totaalPrijs`) VALUES
(7, 2, '2022-12-20', 1, 123),
(8, 2, '2022-12-22', 1, 21000),
(9, 2, '2022-12-22', 1, 500);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `products`
--

CREATE TABLE `products` (
  `ProductID` int(11) NOT NULL,
  `Beschrijving` varchar(265) NOT NULL,
  `Aktief` tinyint(1) NOT NULL,
  `Image` blob NOT NULL,
  `itemGroup` varchar(32) DEFAULT NULL,
  `prijs` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Gegevens worden geëxporteerd voor tabel `products`
--

INSERT INTO `products` (`ProductID`, `Beschrijving`, `Aktief`, `Image`, `itemGroup`, `prijs`) VALUES
(1, 'Groene Ballenbad Ballen', 1, 0x496d616765732f42616c6c656e2f47726f656e6542616c2e706e67, 'Uniek', 1),
(2, 'Rode Ballenbad Ballen', 1, 0x496d616765732f42616c6c656e2f526f646542616c2e706e67, 'Uniek', 1),
(6, 'Blauwe Ballenbad Ballen', 1, 0x496d616765732f42616c6c656e2f426c6175776542616c2e706e67, 'Uniek', 1),
(11, 'Ballenbad Ballen', 1, 0x496d616765732f42616c6c656e2f42616c6c656e4d6978312e706e67, 'Mix', 5),
(15, 'Kerst Ballen', 1, 0x496d616765732f42616c6c656e2f42616c6c656e4d6978322e706e67, 'Mix', 5);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `store`
--

CREATE TABLE `store` (
  `OrderID` int(11) NOT NULL,
  `ProductID` int(11) NOT NULL,
  `amount` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Gegevens worden geëxporteerd voor tabel `store`
--

INSERT INTO `store` (`OrderID`, `ProductID`, `amount`) VALUES
(7, 1, 123),
(7, 1, 500),
(7, 2, 3000),
(7, 12, 3500),
(9, 1, 500);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `users`
--

CREATE TABLE `users` (
  `UserNumber` int(10) NOT NULL,
  `voornaam` varchar(32) DEFAULT NULL,
  `achternaam` varchar(32) DEFAULT NULL,
  `adres` varchar(32) DEFAULT NULL,
  `rechten` varchar(8) DEFAULT NULL,
  `Password` varchar(128) NOT NULL,
  `Actief` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Gegevens worden geëxporteerd voor tabel `users`
--

INSERT INTO `users` (`UserNumber`, `voornaam`, `achternaam`, `adres`, `rechten`, `Password`, `Actief`) VALUES
(1, 'user', 'user', 'adres', 'user', '$2y$10$0qDcgTUs9CREfIlgQHG/geus8DVYw.YfqHn2YL55dNm7DMtBL7dYS', 1),
(2, 'admin', 'admin', 'adres', 'admin', '$2y$10$Hzmhn1Y3KYYh7bO13ynO4OuSOy73mbO0esw.9/U2KgHsl1IM0ETni', 1),
(51, 'user2', 'user2n', 'adres', 'user', '$2y$10$6VMb4.wWtJbkjxX.8UzAnO51FcPgA3Xcdl2xT/Ck541U/MITt6r6G', 1);

--
-- Indexen voor geëxporteerde tabellen
--

--
-- Indexen voor tabel `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`OrderID`),
  ADD KEY `UserNumber` (`userNumber`);

--
-- Indexen voor tabel `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`ProductID`);

--
-- Indexen voor tabel `store`
--
ALTER TABLE `store`
  ADD KEY `OrderID` (`OrderID`);

--
-- Indexen voor tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`UserNumber`);

--
-- AUTO_INCREMENT voor geëxporteerde tabellen
--

--
-- AUTO_INCREMENT voor een tabel `orders`
--
ALTER TABLE `orders`
  MODIFY `OrderID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT voor een tabel `products`
--
ALTER TABLE `products`
  MODIFY `ProductID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT voor een tabel `users`
--
ALTER TABLE `users`
  MODIFY `UserNumber` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- Beperkingen voor geëxporteerde tabellen
--

--
-- Beperkingen voor tabel `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `UserNumber` FOREIGN KEY (`userNumber`) REFERENCES `users` (`UserNumber`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
