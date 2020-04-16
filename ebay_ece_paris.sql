-- phpMyAdmin SQL Dump
-- version 4.9.2
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le :  jeu. 16 avr. 2020 à 07:45
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
  `Adresse_ligne1` varchar(255) DEFAULT NULL,
  `Adresse_ligne2` varchar(255) DEFAULT NULL,
  `Ville` varchar(255) DEFAULT NULL,
  `Code_postal` int(11) DEFAULT NULL,
  `Pays` varchar(255) DEFAULT NULL,
  `Telephone` int(10) DEFAULT NULL,
  `Type_carte` varchar(255) DEFAULT NULL,
  `Numero_carte` int(11) DEFAULT NULL,
  `Nom_carte` varchar(255) DEFAULT NULL,
  `Date_exp_carte` varchar(255) DEFAULT NULL,
  `Code_securite` int(11) DEFAULT NULL,
  `Solde` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `acheteur`
--

INSERT INTO `acheteur` (`ID`, `Adresse_ligne1`, `Adresse_ligne2`, `Ville`, `Code_postal`, `Pays`, `Telephone`, `Type_carte`, `Numero_carte`, `Nom_carte`, `Date_exp_carte`, `Code_securite`, `Solde`) VALUES
(30, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(29, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(32, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `encherir`
--

DROP TABLE IF EXISTS `encherir`;
CREATE TABLE IF NOT EXISTS `encherir` (
  `ID_enchere` int(255) NOT NULL,
  `ID_acheteur` int(255) NOT NULL,
  `ID_item` int(11) DEFAULT NULL,
  `Prix_acheteur` int(255) DEFAULT NULL,
  PRIMARY KEY (`ID_enchere`,`ID_acheteur`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `item`
--

DROP TABLE IF EXISTS `item`;
CREATE TABLE IF NOT EXISTS `item` (
  `ID_item` int(11) NOT NULL AUTO_INCREMENT,
  `Nom_item` varchar(255) NOT NULL,
  `ID_vendeur` int(11) DEFAULT NULL,
  `ID_type_vente` varchar(255) DEFAULT NULL,
  `Description` varchar(255) DEFAULT NULL,
  `Categorie` varchar(255) DEFAULT NULL,
  `Prix` int(11) DEFAULT NULL,
  `Video` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ID_item`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=87 DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `item`
--

INSERT INTO `item` (`ID_item`, `Nom_item`, `ID_vendeur`, `ID_type_vente`, `Description`, `Categorie`, `Prix`, `Video`) VALUES
(86, 'Meuble', 5, ' enchere', 'Meuble en enchere', 'Farraille_tresor', 50, ''),
(85, 'Ipad', 4, ' enchere', 'Ipad au enchere', 'VIP', 100, ''),
(80, 'Ipad Pro', 4, ' offre', 'Ipad en offre', 'VIP', 1090, ''),
(79, 'Ipad Pro', 4, 'achat_immediat ', 'Ipad Pro', 'VIP', 1090, '');

-- --------------------------------------------------------

--
-- Structure de la table `liste_enchere`
--

DROP TABLE IF EXISTS `liste_enchere`;
CREATE TABLE IF NOT EXISTS `liste_enchere` (
  `ID_enchere` int(255) NOT NULL AUTO_INCREMENT,
  `ID_item` int(255) DEFAULT NULL,
  `Date_debut` date DEFAULT NULL,
  `Heure_debut` time DEFAULT NULL,
  `Date_fin` date DEFAULT NULL,
  `Heure_fin` time DEFAULT NULL,
  `Prix_premier` int(11) NOT NULL,
  `Prix_second` int(11) DEFAULT NULL,
  `Prix` int(11) NOT NULL,
  PRIMARY KEY (`ID_enchere`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `liste_enchere`
--

INSERT INTO `liste_enchere` (`ID_enchere`, `ID_item`, `Date_debut`, `Heure_debut`, `Date_fin`, `Heure_fin`, `Prix_premier`, `Prix_second`, `Prix`) VALUES
(6, 85, '2020-04-17', '12:12:00', '2020-04-18', '12:12:00', 100, NULL, 100),
(7, 86, '2020-04-18', '11:11:00', '2020-04-18', '12:12:00', 50, NULL, 50);

-- --------------------------------------------------------

--
-- Structure de la table `meilleur_offre`
--

DROP TABLE IF EXISTS `meilleur_offre`;
CREATE TABLE IF NOT EXISTS `meilleur_offre` (
  `ID_acheteur` int(255) NOT NULL,
  `ID_vendeur` int(255) NOT NULL,
  `ID_item` int(11) NOT NULL,
  `Prix_acheteur` int(255) DEFAULT NULL,
  `Prix_vendeur` int(255) DEFAULT NULL,
  `Tentative` int(255) DEFAULT NULL,
  `Statut` int(255) DEFAULT NULL,
  PRIMARY KEY (`ID_acheteur`,`ID_vendeur`,`ID_item`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `panier`
--

DROP TABLE IF EXISTS `panier`;
CREATE TABLE IF NOT EXISTS `panier` (
  `ID` int(11) NOT NULL,
  `ID_item` int(11) NOT NULL,
  `ID_type_vente` varchar(255) NOT NULL,
  PRIMARY KEY (`ID_item`,`ID`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `personne`
--

DROP TABLE IF EXISTS `personne`;
CREATE TABLE IF NOT EXISTS `personne` (
  `ID` int(255) NOT NULL AUTO_INCREMENT,
  `Nom` varchar(255) DEFAULT NULL,
  `Prenom` varchar(255) DEFAULT NULL,
  `Email` varchar(255) DEFAULT NULL,
  `Statut` int(11) DEFAULT NULL,
  `Mot_de_passe` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=33 DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `personne`
--

INSERT INTO `personne` (`ID`, `Nom`, `Prenom`, `Email`, `Statut`, `Mot_de_passe`) VALUES
(1, 'Cai', 'Emilie', 'emilie@gmail.com', 1, 'azerty'),
(2, 'Sivapalan', 'Sutharsan', 's.sutharssan@gmail.com', 1, 'azerty'),
(3, 'Bruant', 'Camille', 'camille@gmail.com', 1, 'azerty'),
(4, 'Patrick', 'Claude', 'claude@gmail.com', 2, 'claude@gmail.com'),
(5, 'Gilles', 'Francois', 'francois@gmail.com', 2, 'francois@gmail.com'),
(30, 'NomAcheteur', 'PrenomAcheteur', 'Acheteur@gmail.com', 3, 'azerty'),
(29, 'Coucou', 'coucou', 'coucou@gmail.com', 3, 'coucou'),
(32, 'Pigeon', 'Pigeon', 'pigeon@gmail.com', 3, 'pigeon');

-- --------------------------------------------------------

--
-- Structure de la table `photo`
--

DROP TABLE IF EXISTS `photo`;
CREATE TABLE IF NOT EXISTS `photo` (
  `Nom_photo` varchar(255) NOT NULL,
  `ID_item` int(70) NOT NULL,
  `Direction` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`Nom_photo`,`ID_item`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `photo`
--

INSERT INTO `photo` (`Nom_photo`, `ID_item`, `Direction`) VALUES
('ipad.jpg', 85, NULL),
('ipad_2.jpg', 80, NULL),
('ipad.jpg', 80, NULL),
('ipad_2.jpg', 79, NULL),
('ipad.jpg', 79, NULL),
('ipad_2.jpg', 85, NULL),
('meuble.jpg', 86, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `vendeur`
--

DROP TABLE IF EXISTS `vendeur`;
CREATE TABLE IF NOT EXISTS `vendeur` (
  `ID` int(11) NOT NULL,
  `Pseudo` varchar(255) NOT NULL,
  `ID_photo` varchar(255) DEFAULT NULL,
  `ID_image_fond` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`Pseudo`,`ID`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `vendeur`
--

INSERT INTO `vendeur` (`ID`, `Pseudo`, `ID_photo`, `ID_image_fond`) VALUES
(4, 'Pat', NULL, NULL),
(5, 'gil', NULL, NULL);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
