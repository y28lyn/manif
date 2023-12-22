-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : ven. 22 déc. 2023 à 17:16
-- Version du serveur : 10.4.27-MariaDB
-- Version de PHP : 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `association`
--

-- --------------------------------------------------------

--
-- Structure de la table `activité`
--

CREATE TABLE `activité` (
  `id_activité` int(11) NOT NULL,
  `NomAct` varchar(255) DEFAULT NULL,
  `Description` text DEFAULT NULL,
  `num_resp` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `activité`
--

INSERT INTO `activité` (`id_activité`, `NomAct`, `Description`, `num_resp`) VALUES
(14, 'Tennis', 'Faites un match de tennis!', 1),
(15, 'GP Explorer', 'Observer des streamers s\'affronter dans une course effrénée !', 2),
(16, 'Bowling', 'Jouez au bowling !', 1);

-- --------------------------------------------------------

--
-- Structure de la table `avoir`
--

CREATE TABLE `avoir` (
  `id_activite` int(11) NOT NULL,
  `id_creneau` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `avoir`
--

INSERT INTO `avoir` (`id_activite`, `id_creneau`) VALUES
(14, 1),
(15, 1),
(16, 1);

-- --------------------------------------------------------

--
-- Structure de la table `creneau`
--

CREATE TABLE `creneau` (
  `id_creneau` int(11) NOT NULL,
  `heure_debut` time DEFAULT NULL,
  `heure_fin` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `creneau`
--

INSERT INTO `creneau` (`id_creneau`, `heure_debut`, `heure_fin`) VALUES
(1, '06:00:00', '10:00:00'),
(2, '09:00:00', '12:00:00');

-- --------------------------------------------------------

--
-- Structure de la table `participant`
--

CREATE TABLE `participant` (
  `num_participant` int(11) NOT NULL,
  `nom` varchar(255) DEFAULT NULL,
  `prenom` varchar(255) DEFAULT NULL,
  `mail` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `participant`
--

INSERT INTO `participant` (`num_participant`, `nom`, `prenom`, `mail`) VALUES
(8, 'Beirade', 'Ilyes', 'i.beirade@gmail.com'),
(10, 'BEN HADJ AMOR', 'Aymane', 'a.benhadjamor@gmail.com'),
(11, 'BEN HADJ AMOR', 'Jenna', 'j.benhadj@gmail.com');

-- --------------------------------------------------------

--
-- Structure de la table `participation`
--

CREATE TABLE `participation` (
  `id_part` int(11) NOT NULL,
  `id_activite` int(11) DEFAULT NULL,
  `num_participant` int(11) DEFAULT NULL,
  `id_creneau` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `participation`
--

INSERT INTO `participation` (`id_part`, `id_activite`, `num_participant`, `id_creneau`) VALUES
(65, 14, 11, 1),
(71, 16, 11, 1),
(73, 15, 8, 1);

-- --------------------------------------------------------

--
-- Structure de la table `responsable`
--

CREATE TABLE `responsable` (
  `num_resp` int(11) NOT NULL,
  `Nom` varchar(255) DEFAULT NULL,
  `Prenom` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `responsable`
--

INSERT INTO `responsable` (`num_resp`, `Nom`, `Prenom`) VALUES
(1, 'Lovecraft', 'Karl'),
(2, 'Johnson', 'Anna');

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE `user` (
  `id_user` int(11) NOT NULL,
  `login` varchar(20) NOT NULL,
  `mdp` varchar(30) NOT NULL,
  `role` varchar(255) DEFAULT NULL,
  `id_participant` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id_user`, `login`, `mdp`, `role`, `id_participant`) VALUES
(1, 'admin', 'root', 'admin', NULL),
(7, 'i.beirade', 'root', 'participant', 8),
(9, 'a.benhadj', 'root', 'participant', 10),
(10, 'resp', 'root', 'responsable', NULL),
(11, 'j.benhadj', 'root', 'participant', 11);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `activité`
--
ALTER TABLE `activité`
  ADD PRIMARY KEY (`id_activité`),
  ADD KEY `num_resp` (`num_resp`);

--
-- Index pour la table `avoir`
--
ALTER TABLE `avoir`
  ADD PRIMARY KEY (`id_activite`,`id_creneau`),
  ADD KEY `id_creneau` (`id_creneau`);

--
-- Index pour la table `creneau`
--
ALTER TABLE `creneau`
  ADD PRIMARY KEY (`id_creneau`);

--
-- Index pour la table `participant`
--
ALTER TABLE `participant`
  ADD PRIMARY KEY (`num_participant`);

--
-- Index pour la table `participation`
--
ALTER TABLE `participation`
  ADD PRIMARY KEY (`id_part`),
  ADD KEY `id_activite` (`id_activite`),
  ADD KEY `num_participant` (`num_participant`),
  ADD KEY `id_creneau` (`id_creneau`);

--
-- Index pour la table `responsable`
--
ALTER TABLE `responsable`
  ADD PRIMARY KEY (`num_resp`);

--
-- Index pour la table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`),
  ADD KEY `fk_user_participant` (`id_participant`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `activité`
--
ALTER TABLE `activité`
  MODIFY `id_activité` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT pour la table `creneau`
--
ALTER TABLE `creneau`
  MODIFY `id_creneau` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `participant`
--
ALTER TABLE `participant`
  MODIFY `num_participant` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT pour la table `participation`
--
ALTER TABLE `participation`
  MODIFY `id_part` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;

--
-- AUTO_INCREMENT pour la table `responsable`
--
ALTER TABLE `responsable`
  MODIFY `num_resp` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `activité`
--
ALTER TABLE `activité`
  ADD CONSTRAINT `activité_ibfk_1` FOREIGN KEY (`num_resp`) REFERENCES `responsable` (`num_resp`);

--
-- Contraintes pour la table `avoir`
--
ALTER TABLE `avoir`
  ADD CONSTRAINT `avoir_ibfk_1` FOREIGN KEY (`id_activite`) REFERENCES `activité` (`id_activité`),
  ADD CONSTRAINT `avoir_ibfk_2` FOREIGN KEY (`id_creneau`) REFERENCES `creneau` (`id_creneau`);

--
-- Contraintes pour la table `participation`
--
ALTER TABLE `participation`
  ADD CONSTRAINT `participation_ibfk_1` FOREIGN KEY (`id_activite`) REFERENCES `activité` (`id_activité`),
  ADD CONSTRAINT `participation_ibfk_2` FOREIGN KEY (`num_participant`) REFERENCES `participant` (`num_participant`),
  ADD CONSTRAINT `participation_ibfk_3` FOREIGN KEY (`id_creneau`) REFERENCES `creneau` (`id_creneau`);

--
-- Contraintes pour la table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `fk_user_participant` FOREIGN KEY (`id_participant`) REFERENCES `participant` (`num_participant`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
