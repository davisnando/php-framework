-- phpMyAdmin SQL Dump
-- version 4.6.4deb1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Gegenereerd op: 20 apr 2017 om 14:20
-- Serverversie: 5.7.17-0ubuntu0.16.10.1
-- PHP-versie: 7.0.15-0ubuntu0.16.10.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `Framework`
--

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `Perm`
--

CREATE TABLE `Perm` (
  `idPerm` int(11) NOT NULL,
  `description` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Gegevens worden geëxporteerd voor tabel `Perm`
--

INSERT INTO `Perm` (`idPerm`, `description`) VALUES
(1, 'adminpanel'),
(2, 'checkuser'),
(3, 'updateuser'),
(4, 'createuser'),
(5, 'changerole'),
(6, 'changeUserRole'),
(7, 'createRole'),
(8, 'Role');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `permRole`
--

CREATE TABLE `permRole` (
  `idpermRole` int(11) NOT NULL,
  `idRole` int(11) NOT NULL,
  `idPerm` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Gegevens worden geëxporteerd voor tabel `permRole`
--

INSERT INTO `permRole` (`idpermRole`, `idRole`, `idPerm`) VALUES
(1, 1, 1),
(2, 1, 2),
(3, 1, 3),
(4, 1, 4),
(5, 1, 5),
(6, 1, 6),
(7, 1, 7),
(8, 5, 1),
(9, 5, 3),
(10, 5, 4),
(11, 5, 2),
(12, 1, 8);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `Personal`
--

CREATE TABLE `Personal` (
  `idPersonal` int(11) NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Gegevens worden geëxporteerd voor tabel `Personal`
--

INSERT INTO `Personal` (`idPersonal`, `firstname`, `lastname`) VALUES

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `Role`
--

CREATE TABLE `Role` (
  `idRole` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Gegevens worden geëxporteerd voor tabel `Role`
--

INSERT INTO `Role` (`idRole`, `name`) VALUES
(1, 'admin'),
(2, 'Default'),

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `userRole`
--

CREATE TABLE `userRole` (
  `iduserRole` int(11) NOT NULL,
  `idUser` int(11) NOT NULL,
  `idRole` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Gegevens worden geëxporteerd voor tabel `userRole`
--

INSERT INTO `userRole` (`iduserRole`, `idUser`, `idRole`) VALUES

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `Users`
--

CREATE TABLE `Users` (
  `idUsers` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `idPersonal` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexen voor geëxporteerde tabellen
--

--
-- Indexen voor tabel `Perm`
--
ALTER TABLE `Perm`
  ADD PRIMARY KEY (`idPerm`);

--
-- Indexen voor tabel `permRole`
--
ALTER TABLE `permRole`
  ADD PRIMARY KEY (`idpermRole`),
  ADD KEY `Role` (`idRole`),
  ADD KEY `Permission` (`idPerm`);

--
-- Indexen voor tabel `Personal`
--
ALTER TABLE `Personal`
  ADD PRIMARY KEY (`idPersonal`);

--
-- Indexen voor tabel `Role`
--
ALTER TABLE `Role`
  ADD PRIMARY KEY (`idRole`);

--
-- Indexen voor tabel `userRole`
--
ALTER TABLE `userRole`
  ADD PRIMARY KEY (`iduserRole`),
  ADD KEY `User` (`idUser`),
  ADD KEY `Role` (`idRole`);

--
-- Indexen voor tabel `Users`
--
ALTER TABLE `Users`
  ADD PRIMARY KEY (`idUsers`),
  ADD KEY `idPersonal` (`idPersonal`);

--
-- AUTO_INCREMENT voor geëxporteerde tabellen
--

--
-- AUTO_INCREMENT voor een tabel `Perm`
--
ALTER TABLE `Perm`
  MODIFY `idPerm` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT voor een tabel `permRole`
--
ALTER TABLE `permRole`
  MODIFY `idpermRole` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
--
-- AUTO_INCREMENT voor een tabel `Personal`
--
ALTER TABLE `Personal`
  MODIFY `idPersonal` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
--
-- AUTO_INCREMENT voor een tabel `Role`
--
ALTER TABLE `Role`
  MODIFY `idRole` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT voor een tabel `userRole`
--
ALTER TABLE `userRole`
  MODIFY `iduserRole` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
--
-- AUTO_INCREMENT voor een tabel `Users`
--
ALTER TABLE `Users`
  MODIFY `idUsers` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
--
-- Beperkingen voor geëxporteerde tabellen
--

--
-- Beperkingen voor tabel `permRole`
--
ALTER TABLE `permRole`
  ADD CONSTRAINT `Permissions` FOREIGN KEY (`idPerm`) REFERENCES `Perm` (`idPerm`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `Role` FOREIGN KEY (`idRole`) REFERENCES `Role` (`idRole`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Beperkingen voor tabel `userRole`
--
ALTER TABLE `userRole`
  ADD CONSTRAINT `rollen` FOREIGN KEY (`idRole`) REFERENCES `Role` (`idRole`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `users` FOREIGN KEY (`idUser`) REFERENCES `Users` (`idUsers`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Beperkingen voor tabel `Users`
--
ALTER TABLE `Users`
  ADD CONSTRAINT `PersonalTable` FOREIGN KEY (`idPersonal`) REFERENCES `Personal` (`idPersonal`) ON DELETE NO ACTION ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
