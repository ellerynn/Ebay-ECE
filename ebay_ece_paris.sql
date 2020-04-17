-- phpMyAdmin SQL Dump
-- version 4.9.2
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le :  ven. 17 avr. 2020 à 17:00
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
(30, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

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
) ENGINE=MyISAM AUTO_INCREMENT=89 DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `item`
--

INSERT INTO `item` (`ID_item`, `Nom_item`, `ID_vendeur`, `ID_type_vente`, `Description`, `Categorie`, `Prix`, `Video`) VALUES
(86, 'Monnaie antique', 5, ' enchere', 'Monnaie romaine\r\nMÃ©tal : Or\r\nAnnÃ©e : 147-175\r\nValeur faciale : Aureus\r\nPersonnage principal : Faustina II\r\nPoids : 7.29 gr', 'Musee', 50, ''),
(85, 'Fila Disruptor II', 5, ' offre', 'Marque Fila', 'VIP', 60, ''),
(82, 'Ipad Pro', 4, 'achat_immediat ', 'Ipad Pro neuf', 'VIP', 1090, ''),
(83, 'Tableau abstrait', 4, ' enchere', 'Que voyez-vous ?', 'Musee', 100, ''),
(84, 'Sneaker Dior', 5, 'achat_immediat offre', 'Type : tissu\r\nMarque : Dior', 'VIP', 890, ''),
(87, 'Album Euros', 5, 'achat_immediat enchere', 'Album Euro 30 piÃ¨ces de 2 euros', 'Farraille_tresor', 300, ''),
(88, 'Lampe Harry Potter', 5, ' offre', 'Lampe Harry Potter ', 'Farraille_tresor', 50, '');

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
  `Fin` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`ID_enchere`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `liste_enchere`
--

INSERT INTO `liste_enchere` (`ID_enchere`, `ID_item`, `Date_debut`, `Heure_debut`, `Date_fin`, `Heure_fin`, `Prix_premier`, `Prix_second`, `Prix`, `Fin`) VALUES
(7, 83, '1212-12-12', '12:12:00', '1313-12-13', '13:13:00', 100, NULL, 100, 0),
(8, 86, '1313-12-13', '12:12:00', '1414-12-14', '14:14:00', 50, NULL, 50, 0),
(9, 87, '1414-12-14', '12:12:00', '1515-12-15', '13:13:00', 60, NULL, 60, 0);

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
) ENGINE=MyISAM AUTO_INCREMENT=32 DEFAULT CHARSET=utf8;

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
(29, 'Coucou', 'coucou', 'coucou@gmail.com', 3, 'coucou');

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
('fila2.jpg', 85, NULL),
('fila.jpg', 85, NULL),
('dior.jpg', 84, NULL),
('tableau.jpg', 83, NULL),
('ipad_2.jpg', 82, NULL),
('ipad.jpg', 82, NULL),
('monnaieRomaine.jpg', 86, NULL),
('monnaieRomaine2.jpg', 86, NULL),
('albumEuro.jpg', 87, NULL),
('LampeHarryPotter.jpg', 88, NULL);

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
(4, 'Pat', 'photo_defaut.jpg', 'default_fond.jpg'),
(5, 'gil', NULL, NULL);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
