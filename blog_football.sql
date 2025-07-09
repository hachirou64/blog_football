-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mer. 09 juil. 2025 à 18:08
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `blog_football`
--

-- --------------------------------------------------------

--
-- Structure de la table `articles`
--

CREATE TABLE `articles` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `author_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `views` int(11) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `articles`
--

INSERT INTO `articles` (`id`, `title`, `content`, `author_id`, `category_id`, `views`, `created_at`, `updated_at`) VALUES
(1, 'Le Clasico : Plus qu\'un Match, une Histoire', 'Le football regorge de rivalités légendaires, mais peu atteignent l\'ampleur et l\'intensité du \"Clasico\" entre le Real Madrid et le FC Barcelone. Ce n\'est pas seulement un match de football ; c\'est un choc culturel, politique et historique qui captive des millions de spectateurs à travers le monde.\r\n\r\nUne Rivalité Ancrée dans l\'Histoire\r\n\r\nLa confrontation entre Madrid et Barcelone est le reflet de tensions régionales et nationales en Espagne. Le Real Madrid, souvent perçu comme le club de l\'établissement espagnol et de la capitale, contraste avec le FC Barcelone, symbole de l\'identité catalane et de ses aspirations. Cette dimension dépasse largement le cadre sportif et confère à chaque rencontre une charge émotionnelle unique.\r\n\r\nLes Géants sur le Terrain\r\n\r\nAu fil des décennies, le Clasico a été le théâtre de performances individuelles et collectives inoubliables. Des figures emblématiques comme Alfredo Di Stéfano et Johan Cruyff ont écrit les premières pages de cette légende. Plus récemment, la rivalité a été sublimée par la décennie où Lionel Messi et Cristiano Ronaldo se sont affrontés, offrant des duels au sommet qui resteront gravés dans les mémoires. Chaque but, chaque dribble, chaque parade prenait une importance décuplée dans ces affrontements.\r\n\r\nDes Moments Iconiques\r\n\r\nQui pourrait oublier le 5-0 de Barcelone sur le Real en 2010 sous Guardiola, ou la tête rageuse de Zidane en Ligue des Champions qui a souvent rappelé la grandeur des Madrilènes ? Chaque Clasico est une opportunité d\'écrire une nouvelle page de l\'histoire du football. Qu\'il s\'agisse d\'une victoire écrasante, d\'un match nul arraché à la dernière minute, ou d\'un coup de génie individuel, le Clasico ne laisse jamais indifférent.\r\n\r\nL\'Impact Mondial\r\n\r\nAu-delà de l\'Espagne, le Clasico est un événement global. Des continents entiers s\'arrêtent pour suivre ces 90 minutes où l\'orgueil et la passion débordent. Les transferts de joueurs entre les deux clubs (même s\'ils sont rares et souvent controversés, comme celui de Luís Figo) ne font qu\'alimenter la flamme.\r\n\r\nLe Futur du Clasico\r\n\r\nAlors que l\'ère Messi-Ronaldo est derrière nous, de nouvelles générations de stars émergent, prêtes à prendre le relais. Le Clasico continue d\'être un test décisif pour les joueurs, les entraîneurs et les stratégies. Il représente toujours le sommet du football de club et promet encore de nombreux moments d\'anthologie.\r\n\r\n', 1, 2, 8, '2025-06-28 10:31:05', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `categories`
--

INSERT INTO `categories` (`id`, `name`, `created_at`) VALUES
(2, 'Actualités', '2025-07-01 14:17:21'),
(6, 'Analyse Tactique', '2025-07-01 14:17:21'),
(8, 'Transferts', '2025-07-01 14:17:21'),
(9, 'Histoire du Foot', '2025-07-01 14:17:21'),
(10, 'Interviews', '2025-07-01 14:17:21');

-- --------------------------------------------------------

--
-- Structure de la table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `content` text NOT NULL,
  `article_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `comments`
--

INSERT INTO `comments` (`id`, `content`, `article_id`, `user_id`, `created_at`) VALUES
(1, 'votre article est intessant', 1, 1, '2025-06-28 10:32:13');

-- --------------------------------------------------------

--
-- Structure de la table `likes`
--

CREATE TABLE `likes` (
  `id` int(11) NOT NULL,
  `article_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `reaction` tinyint(1) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `posts`
--

INSERT INTO `posts` (`id`, `user_id`, `category_id`, `title`, `content`, `image_url`, `created_at`, `updated_at`) VALUES
(1, 1, 0, 'Le VAR et la Goal-Line Technologie : Révolution ou Controverse ?', 'Le football, sport roi mondial, a toujours été un mélange de passion, de talent et parfois de décisions arbitrales controversées. Cependant, l\'avènement de la technologie ces dernières années a profondément modifié la façon dont le jeu est officié et perçu. Parmi les innovations majeures, la Goal-Line Technology (GLT) et l\'Assistance Vidéo à l\'Arbitrage (VAR) sont sans doute les plus impactantes.\r\n\r\nLa Goal-Line Technology, introduite dans les grandes compétitions, a été largement saluée. Son objectif est simple : déterminer avec certitude si le ballon a franchi la ligne de but en totalité. Fini les débats houleux sur les \"buts fantômes\" ; la GLT apporte une réponse binaire et quasi instantanée, augmentant ainsi l\'équité des matchs sur ce point précis. Sa mise en œuvre, discrète et efficace, a rapidement gagné l\'approbation des joueurs, des entraîneurs et des supporters.\r\n\r\nLe cas du VAR est bien plus complexe. Introduit pour corriger les \"erreurs claires et évidentes\" qui changent le cours d\'un match (buts, penalties, cartons rouges directs, erreur sur l\'identité d\'un joueur), le VAR a généré un débat passionné. D\'un côté, il permet de corriger des injustices flagrantes et assure une plus grande précision des décisions. Des situations qui auraient autrefois ruiné un match ou une saison peuvent désormais être révisées.\r\n\r\nCependant, le VAR a aussi ses détracteurs. Les interruptions du jeu sont souvent longues, cassant le rythme et l\'émotion du match. La subjectivité de certaines interprétations, même avec la vidéo, reste une source de frustration. De plus, la communication avec le public n\'est pas toujours optimale, laissant parfois les spectateurs dans l\'incompréhension totale pendant les révisions. La notion même d\'\"erreur claire et évidente\" est sujette à interprétation et varie d\'un arbitre à l\'autre, voire d\'une ligue à l\'autre.\r\n\r\nEn conclusion, la technologie a indéniablement apporté plus de justice et de précision au football. La Goal-Line Technologie en est un exemple parfait d\'intégration réussie. Le VAR, quant à lui, est une innovation prometteuse mais qui nécessite encore des ajustements pour trouver le juste équilibre entre la correction des erreurs et la préservation de la fluidité et de l\'âme du jeu. Le débat sur son utilisation est loin d\'être clos, mais il témoigne de la volonté constante d\'améliorer le sport.\r\n\r\nImage (optionnel) : Vous pouvez trouver une image en ligne d\'un écran VAR ou d\'un arbitre utilisant le signe VAR pour l\'upload. Sinon, vous pouvez laisser le champ vide pour l\'instant.\r\n\r\nCatégorie (optionnel) : Si vous avez déjà configuré les catégories, vous pourriez le classer sous \"Technologie\", \"Règles du jeu\", ou \"Analyse\". Sinon, laissez vide pour le moment.', '/blog_football/uploads/img_6860f645e76a4.jpg', '2025-06-29 08:16:05', '2025-06-29 08:16:05');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` varchar(50) DEFAULT 'reader',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `role`, `created_at`) VALUES
(1, 'root', 'root@gmail.com', '$2y$10$hnVJMXwYBFawHLABxD8viephVwMBdh1cERcyma1KLgCgCTdQsdXtm', 'admin', '2025-06-28 09:37:02'),
(3, 'latifou', 'latifou@gmail.com', '$2y$10$L6YBBFu2hAgXuTNofg8oU.aFKrZW9DLgHs.JvB9KGvvAjPU1QYwji', 'utilisateur', '2025-07-01 15:10:37');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `articles`
--
ALTER TABLE `articles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `author_id` (`author_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Index pour la table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Index pour la table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `article_id` (`article_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `article_id` (`article_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `articles`
--
ALTER TABLE `articles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pour la table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `likes`
--
ALTER TABLE `likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `articles`
--
ALTER TABLE `articles`
  ADD CONSTRAINT `articles_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `articles_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `likes`
--
ALTER TABLE `likes`
  ADD CONSTRAINT `likes_ibfk_1` FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `likes_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
