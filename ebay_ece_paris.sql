-- phpMyAdmin SQL Dump
-- version 4.9.2
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le :  Dim 12 avr. 2020 à 18:43
-- Version du serveur :  10.4.10-MariaDB
-- Version de PHP :  7.3.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `ebay ece paris`
--

-- --------------------------------------------------------

--
-- Structure de la table `acheteur`
--

DROP TABLE IF EXISTS `acheteur`;
CREATE TABLE IF NOT EXISTS `acheteur` (
  `ID` int(11) NOT NULL,
  `ID_panier` int(11) NOT NULL AUTO_INCREMENT,
  `Adresse_ligne1` varchar(255) DEFAULT NULL,
  `Adresse_ligne2` varchar(255) DEFAULT NULL,
  `Ville` varchar(255) DEFAULT NULL,
  `Code_Postal` int(11) DEFAULT NULL,
  `Pays` varchar(255) DEFAULT NULL,
  `Telephone` int(10) DEFAULT NULL,
  `Type_carte` varchar(255) DEFAULT NULL,
  `Numero_carte` int(11) DEFAULT NULL,
  `Nom_carte` varchar(255) DEFAULT NULL,
  `Date_exp_carte` varchar(255) DEFAULT NULL,
  `Code_securite` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`,`ID_panier`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `acheteur`
--

INSERT INTO `acheteur` (`ID`, `ID_panier`, `Adresse_ligne1`, `Adresse_ligne2`, `Ville`, `Code_Postal`, `Pays`, `Telephone`, `Type_carte`, `Numero_carte`, `Nom_carte`, `Date_exp_carte`, `Code_securite`) VALUES
(21, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(22, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(23, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `item`
--

DROP TABLE IF EXISTS `item`;
CREATE TABLE IF NOT EXISTS `item` (
  `ID_item` int(11) NOT NULL AUTO_INCREMENT,
  `Nom_item` varchar(255) NOT NULL,
  `Description` varchar(255) NOT NULL,
  `Video` varchar(255) NOT NULL,
  `Categorie` varchar(255) NOT NULL,
  `Prix` int(11) NOT NULL,
  `ID_type_vente` varchar(255) NOT NULL,
  PRIMARY KEY (`ID_item`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `panier`
--

DROP TABLE IF EXISTS `panier`;
CREATE TABLE IF NOT EXISTS `panier` (
  `ID_panier` int(11) NOT NULL,
  `ID_item` int(11) NOT NULL,
  `ID_type_vente` varchar(255) NOT NULL,
  PRIMARY KEY (`ID_item`,`ID_type_vente`,`ID_panier`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `personne`
--

DROP TABLE IF EXISTS `personne`;
CREATE TABLE IF NOT EXISTS `personne` (
  `ID` int(255) NOT NULL AUTO_INCREMENT,
  `Nom` varchar(255) NOT NULL,
  `Prenom` varchar(255) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `ID_statut` int(11) NOT NULL,
  `Mot_de_passe` varchar(255) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=24 DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `personne`
--

INSERT INTO `personne` (`ID`, `Nom`, `Prenom`, `Email`, `ID_statut`, `Mot_de_passe`) VALUES
(1, 'Cai', 'Emilie', 'emilie@gmail.com', 1, ''),
(2, 'Sivapalan', 'Sutharsan', 's.sutharssan@gmail.com', 1, ''),
(3, 'Bruant', 'Camille', 'camille@gmail.com', 1, ''),
(4, 'Patrick', 'Claude', 'claude@gmail.com', 2, ''),
(5, 'Gilles', 'Francois', 'francois@gmail.com', 3, ''),
(8, 'Sivapalan', 'Subramaniam', 's.suan@gmail.com', 3, 'qfqeef'),
(9, 'asa', 'asasas', 'sasas', 3, 'sasasa'),
(10, 'Sivapalan', 'Subramaniam', 'fefefe', 3, 'fefefe'),
(13, 'Sivapalan', 'Sutharsan', 'pppppppppppp', 3, 'ppppppppppp'),
(14, 'Sivapalan', 'Subramaniam', 'aaaaaaaaaaaaaaaaaaa', 3, 'aaaaaaaaaaaaaaaaaaa'),
(15, 'Sivapalan', 'Sutharsan', 'bbbbbbbbbbbb', 3, 'bbbbbbbbbb'),
(16, 'Sivapalan', 'Sutharsan', 'ccccccccccccc', 3, 'cccccccccccc'),
(17, 'Sivapalan', 'Sutharsan', 'ddddddddd', 3, 'ddddddddddddddd'),
(18, 'Sivapalan', 'Sutharsan', 'fffffffffffffffffffffff', 3, 'ffffffffffffffffffff'),
(19, 'Sivapalan', 'Sutharsan', 'ggggggggggg', 3, 'gggggggggggggg'),
(20, 'Sivapalan', 'Sutharsan', 'hhhhhhhhhhhhhhhhhh', 3, 'hhhhhhhhhhhhhhhhh'),
(21, 'Sivapalan', 'Subramaniam', 'iiiiiiiiiiiiiiiii', 3, 'iiiiiiiiiiiiii'),
(22, 'Sivapalan', 'Subramaniam', 'llllllllllllll', 3, 'llllllllllllllll'),
(23, 'Sivapalan', 'Subramaniam', 'mmmmmmmmmmmm', 3, 'mmmmmmmmmm');

-- --------------------------------------------------------

--
-- Structure de la table `photo`
--

DROP TABLE IF EXISTS `photo`;
CREATE TABLE IF NOT EXISTS `photo` (
  `ID_item` int(11) NOT NULL,
  `Nom_photo` varchar(255) NOT NULL,
  PRIMARY KEY (`Nom_photo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `vendeur`
--

DROP TABLE IF EXISTS `vendeur`;
CREATE TABLE IF NOT EXISTS `vendeur` (
  `Pseudo` varchar(255) NOT NULL,
  `ID_photo` varchar(255) NOT NULL,
  `ID_image_fond` varchar(255) NOT NULL,
  `ID` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
