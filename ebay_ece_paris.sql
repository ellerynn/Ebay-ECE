-- phpMyAdmin SQL Dump
-- version 4.9.2
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le :  lun. 20 avr. 2020 à 17:32
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
  `Numero_carte` varchar(255) DEFAULT NULL,
  `Nom_carte` varchar(255) DEFAULT NULL,
  `Date_exp_carte` date DEFAULT NULL,
  `Code_securite` int(11) DEFAULT NULL,
  `Solde` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `acheteur`
--

INSERT INTO `acheteur` (`ID`, `Adresse_ligne1`, `Adresse_ligne2`, `Ville`, `Code_postal`, `Pays`, `Telephone`, `Type_carte`, `Numero_carte`, `Nom_carte`, `Date_exp_carte`, `Code_securite`, `Solde`) VALUES
(9, '15 Rue turbigo', '', 'Paris', 75002, 'France', 600112233, 'VISA', '19482039485607912', 'Le For Dominique', '2022-04-22', 1111, 1500),
(10, '8 rue de la madeleine', '5e Bat rouge', 'Paris', 75008, 'France', 624123467, 'MASTERCARD', '75329405968372039', 'Marin', '2020-04-13', 2222, 1500),
(11, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `contact`
--

DROP TABLE IF EXISTS `contact`;
CREATE TABLE IF NOT EXISTS `contact` (
  `ID_message` int(11) NOT NULL AUTO_INCREMENT,
  `ID_admin` int(11) NOT NULL,
  `ID_acheteur` int(11) NOT NULL,
  `Message` text DEFAULT NULL,
  `Reponse` int(11) DEFAULT NULL,
  `Objet` text DEFAULT NULL,
  PRIMARY KEY (`ID_message`) USING BTREE,
  KEY `contact_ibfk_1` (`ID_acheteur`),
  KEY `contact_ibfk_2` (`ID_admin`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
  PRIMARY KEY (`ID_enchere`,`ID_acheteur`) USING BTREE,
  KEY `encherir_ibfk_1` (`ID_acheteur`),
  KEY `encherir_ibfk_2` (`ID_item`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `item`
--

DROP TABLE IF EXISTS `item`;
CREATE TABLE IF NOT EXISTS `item` (
  `ID_item` int(255) NOT NULL AUTO_INCREMENT,
  `ID_vendeur` int(11) DEFAULT NULL,
  `Nom_item` varchar(255) NOT NULL,
  `ID_type_vente` varchar(255) DEFAULT NULL,
  `Description` varchar(255) DEFAULT NULL,
  `Categorie` varchar(255) DEFAULT NULL,
  `Prix` int(11) DEFAULT NULL,
  `Video` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ID_item`) USING BTREE,
  KEY `item_ibfk_1` (`ID_vendeur`)
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `item`
--

INSERT INTO `item` (`ID_item`, `ID_vendeur`, `Nom_item`, `ID_type_vente`, `Description`, `Categorie`, `Prix`, `Video`) VALUES
(3, 1, 'Fila Disruptor II', 'achat_immediat offre', 'Une paire de chaussures Fila toutes neuves', 'VIP', 50, 'https://www.youtube.com/embed/Fdf1Zyz2FVk?start=99'),
(4, 1, 'Animal crossing New Horizon', ' enchere', 'Une cartouche du jeu Animal crossing New Horizon sur la Nintendo Switch', 'Ferraille_tresor', 46, 'https://www.youtube.com/embed/0enb6_LXWjM'),
(5, 1, 'Nintendo Switch', 'achat_immediat ', 'La console Nintendo Switch', 'Ferraille_tresor', 300, 'https://www.youtube.com/embed/0hLGuxxn_Uw'),
(6, 2, 'Ipad Pro', ' offre', 'Ipad Pro neuf', 'VIP', 1090, ''),
(12, 3, 'Sneaker Dior', ' offre', 'Une paires de sneakers de chez Dior toutes neuves\r\n', 'VIP', 900, ''),
(13, 3, 'J adore Eau de toitelles', 'achat_immediat ', 'Eau de toilettes de chez Dior <br>\r\n150ml.', 'VIP', 142, ''),
(18, 6, 'Masque FFP2', ' offre', 'Masque FFP2 pour contrer le coronavirus', 'Ferraille_tresor', 10, ''),
(43, 4, 'Piano Yamaha', 'achat_immediat offre', 'Yamaha C7X SH2 PE Silent Grand Piano', 'VIP', 5000, ''),
(44, 4, 'Guitar Dragons Breath', 'achat_immediat offre', 'Couleur Sunburst <br>\r\nHousse non incluse <br>\r\nEtui inclus.', 'VIP', 1000, ''),
(45, 6, 'Tableau Penguin Family', ' enchere', 'Tableau 50x70', 'Musee', 26, ''),
(46, 6, 'Tableau abstrait', 'achat_immediat offre', 'Tableau reprÃ©sentant un paysage.', 'Musee', 30, ''),
(47, 6, 'Statue MANOLA en rÃ©sine', ' enchere', 'L.34xH.83cm <br>\r\nl\'oeuvre d\'art design que votre intÃ©rieur attendait !', 'Musee', 140, '');

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
  PRIMARY KEY (`ID_enchere`),
  KEY `liste_enchere_ibfk_1` (`ID_item`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `liste_enchere`
--

INSERT INTO `liste_enchere` (`ID_enchere`, `ID_item`, `Date_debut`, `Heure_debut`, `Date_fin`, `Heure_fin`, `Prix_premier`, `Prix_second`, `Prix`, `Fin`) VALUES
(1, 4, '2020-04-20', '18:00:00', '2020-04-24', '18:00:00', 10, NULL, 10, 2),
(8, 45, '2020-04-20', '20:00:00', '2020-04-26', '20:00:00', 5, NULL, 5, 0),
(9, 47, '2020-04-21', '22:00:00', '2020-04-24', '22:30:00', 10, NULL, 10, 0);

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
  PRIMARY KEY (`ID_acheteur`,`ID_vendeur`,`ID_item`),
  KEY `meilleur_offre_ibfk_2` (`ID_vendeur`),
  KEY `meilleur_offre_ibfk_3` (`ID_item`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `panier`
--

DROP TABLE IF EXISTS `panier`;
CREATE TABLE IF NOT EXISTS `panier` (
  `ID` int(11) NOT NULL,
  `ID_item` int(11) NOT NULL,
  `ID_type_vente` varchar(255) NOT NULL,
  PRIMARY KEY (`ID_item`,`ID`) USING BTREE,
  KEY `panier_ibfk_1` (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `personne`
--

INSERT INTO `personne` (`ID`, `Nom`, `Prenom`, `Email`, `Statut`, `Mot_de_passe`) VALUES
(1, 'Cai', 'Emilie', 'emilie@gmail.com', 1, 'azerty'),
(2, 'Sivapalan', 'Sutharsan', 's.sutharssan@gmail.com', 1, 'azerty'),
(3, 'Bruant', 'Camille', 'camille@gmail.com', 1, 'azerty'),
(4, 'Patrick', 'Claude', 'claude@gmail.com', 2, 'claude@gmail.com'),
(5, 'Gilles', 'Francois', 'francois@gmail.com', 2, 'francois@gmail.com'),
(6, 'Vendeur', 'vendeur', 'vendeur@gmail.com', 2, 'vendeur'),
(9, 'Le For', 'Dominique', 'dominique@gmail.com', 3, 'dominique'),
(10, 'Dublin', 'Marc', 'marc@gmail.com', 3, 'marc'),
(11, 'Acheteur', 'acheteur', 'acheteur@gmail.com', 3, 'acheteur');

-- --------------------------------------------------------

--
-- Structure de la table `photo`
--

DROP TABLE IF EXISTS `photo`;
CREATE TABLE IF NOT EXISTS `photo` (
  `Nom_photo` varchar(255) NOT NULL,
  `ID_item` int(70) NOT NULL,
  `Direction` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`Nom_photo`,`ID_item`) USING BTREE,
  KEY `photo_ibfk_1` (`ID_item`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `photo`
--

INSERT INTO `photo` (`Nom_photo`, `ID_item`, `Direction`) VALUES
('acnh1.jpg', 4, NULL),
('acnh2.jpg', 4, NULL),
('acnh3.jpeg', 4, NULL),
('dior.jpg', 12, NULL),
('DiorJador.jpg', 13, NULL),
('ffp2.jpg', 18, NULL),
('fila.jpg', 3, NULL),
('fila2.jpg', 3, NULL),
('guitar.jpg', 44, NULL),
('ipad.jpg', 6, NULL),
('ipad_2.jpg', 6, NULL),
('pgn.jpg', 45, NULL),
('Piano.jpg', 18, NULL),
('Piano.jpg', 43, NULL),
('statue.jpg', 47, NULL),
('switch.jpg', 5, NULL),
('tableau.jpg', 46, NULL);

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
  PRIMARY KEY (`Pseudo`,`ID`) USING BTREE,
  KEY `vendeur_ibfk_1` (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `vendeur`
--

INSERT INTO `vendeur` (`ID`, `Pseudo`, `ID_photo`, `ID_image_fond`) VALUES
(5, 'gil', 'madonna.jpg', 'fond.jpg'),
(4, 'Pat', 'ipad.jpg', 'dior.jpg'),
(6, 'vendeur1', 'photo_defaut.jpg', 'fond.jpg');

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `acheteur`
--
ALTER TABLE `acheteur`
  ADD CONSTRAINT `acheteur_ibfk_1` FOREIGN KEY (`ID`) REFERENCES `personne` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `contact`
--
ALTER TABLE `contact`
  ADD CONSTRAINT `contact_ibfk_1` FOREIGN KEY (`ID_acheteur`) REFERENCES `personne` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `contact_ibfk_2` FOREIGN KEY (`ID_admin`) REFERENCES `personne` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `encherir`
--
ALTER TABLE `encherir`
  ADD CONSTRAINT `encherir_ibfk_1` FOREIGN KEY (`ID_acheteur`) REFERENCES `personne` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `encherir_ibfk_2` FOREIGN KEY (`ID_item`) REFERENCES `item` (`ID_item`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `item`
--
ALTER TABLE `item`
  ADD CONSTRAINT `item_ibfk_1` FOREIGN KEY (`ID_vendeur`) REFERENCES `personne` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `liste_enchere`
--
ALTER TABLE `liste_enchere`
  ADD CONSTRAINT `liste_enchere_ibfk_1` FOREIGN KEY (`ID_item`) REFERENCES `item` (`ID_item`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `meilleur_offre`
--
ALTER TABLE `meilleur_offre`
  ADD CONSTRAINT `meilleur_offre_ibfk_1` FOREIGN KEY (`ID_acheteur`) REFERENCES `personne` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `meilleur_offre_ibfk_2` FOREIGN KEY (`ID_vendeur`) REFERENCES `personne` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `meilleur_offre_ibfk_3` FOREIGN KEY (`ID_item`) REFERENCES `item` (`ID_item`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `panier`
--
ALTER TABLE `panier`
  ADD CONSTRAINT `panier_ibfk_1` FOREIGN KEY (`ID`) REFERENCES `personne` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `panier_ibfk_2` FOREIGN KEY (`ID_item`) REFERENCES `item` (`ID_item`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `photo`
--
ALTER TABLE `photo`
  ADD CONSTRAINT `photo_ibfk_1` FOREIGN KEY (`ID_item`) REFERENCES `item` (`ID_item`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `vendeur`
--
ALTER TABLE `vendeur`
  ADD CONSTRAINT `vendeur_ibfk_1` FOREIGN KEY (`ID`) REFERENCES `personne` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
