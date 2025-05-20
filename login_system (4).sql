-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 20, 2025 at 06:49 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `login_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `name`, `email`, `password`, `created_at`) VALUES
(3, 'Admin', 'admin@gmail.com', '$2a$12$aH.zZqjKs8X0xJgIhbAb4usuoqfNExs6RvVyZ1ZNrDPUTyVVBgYYG', '2025-05-18 11:27:12');

-- --------------------------------------------------------

--
-- Table structure for table `creators`
--

CREATE TABLE `creators` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL,
  `bio` text NOT NULL,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `creators`
--

INSERT INTO `creators` (`id`, `name`, `role`, `bio`, `image`) VALUES
(1, 'John Andy Abarca', 'Game Developer', 'Passionate about game development and creating immersive experiences.', ''),
(2, 'DM Felicilda', 'Web Developer', 'Specializing in game mechanics and user experience design.', NULL),
(3, 'Angelo Lesiges', 'Co-Developer', 'Creating stunning visuals and character designs.', NULL),
(4, 'Justine Estrella', 'Web Designer', 'Creating stunning visuals and character designs.', NULL),
(5, 'Trixie Talinting', 'Art Director', 'Creating stunning visuals and character designs.', ''),
(6, 'Shane Flores', 'Art Director', 'Creating stunning visuals and character designs.', '');

-- --------------------------------------------------------

--
-- Table structure for table `forum_comments`
--

CREATE TABLE `forum_comments` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_name` varchar(50) NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `forum_comments`
--

INSERT INTO `forum_comments` (`id`, `post_id`, `user_id`, `user_name`, `content`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'andy', 'Letss goo', '2025-05-19 18:31:38', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `forum_likes`
--

CREATE TABLE `forum_likes` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `forum_likes`
--

INSERT INTO `forum_likes` (`id`, `post_id`, `user_id`, `created_at`) VALUES
(1, 1, 1, '2025-05-19 18:31:31');

-- --------------------------------------------------------

--
-- Table structure for table `forum_posts`
--

CREATE TABLE `forum_posts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_name` varchar(50) NOT NULL,
  `category` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `forum_posts`
--

INSERT INTO `forum_posts` (`id`, `user_id`, `user_name`, `category`, `title`, `content`, `created_at`, `updated_at`) VALUES
(1, 3, 'Admin(2)', 'general', 'Welcome to Court Card Slash', 'I thank you for you to try our game called Court Card Slash, we are welcoming your opinions regarding on how to improve our game.', '2025-05-19 18:23:08', '2025-05-19 18:47:06');

-- --------------------------------------------------------

--
-- Table structure for table `games`
--

CREATE TABLE `games` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `score` int(11) DEFAULT NULL,
  `played_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `how_to_play`
--

CREATE TABLE `how_to_play` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `icon` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `how_to_play`
--

INSERT INTO `how_to_play` (`id`, `title`, `content`, `icon`, `created_at`, `updated_at`) VALUES
(1, 'Game Objective', 'Defeat all royal cards before your deck runs out of number cards.\nUse strategic thinking to maximize your chances of winning.', 'fas fa-trophy', '2025-05-19 02:12:22', '2025-05-19 02:12:22'),
(2, 'Gameplay Mechanics', 'Number Cards as Attacks: Use your number cards to attack the royal cards.\nEfficiency is Key: Plan your moves carefully to break your enemies effectively.', 'fas fa-cogs', '2025-05-19 02:12:22', '2025-05-19 02:12:22'),
(3, 'Strategy', 'Card Management: Keep track of your remaining cards and plan your attacks accordingly.\nThink Ahead: Anticipate the moves of the royal cards and adjust your strategy to counter them.', 'fas fa-gamepad', '2025-05-19 02:12:22', '2025-05-19 02:12:22'),
(4, 'Tips & Tricks', 'Familiarize yourself with the strengths and weaknesses of each royal card.\nPractice makes perfect; the more you play, the better your strategic thinking will become.', 'fas fa-lightbulb', '2025-05-19 02:12:22', '2025-05-19 02:12:22');

-- --------------------------------------------------------

--
-- Table structure for table `patch_notes`
--

CREATE TABLE `patch_notes` (
  `id` int(11) NOT NULL,
  `version` varchar(50) NOT NULL,
  `release_date` date NOT NULL,
  `download_link` varchar(255) NOT NULL,
  `features` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patch_notes`
--

INSERT INTO `patch_notes` (`id`, `version`, `release_date`, `download_link`, `features`, `created_at`, `updated_at`) VALUES
(5, '1.0.0 (BETA)', '2025-04-23', 'GODOT GAME\\Court Card Slash (Beta).zip', 'Initial release of Court Card Slash\r\nComplete game mechanics implementation\r\nBasic AI opponent system', '2025-05-20 02:19:39', '2025-05-20 02:19:39'),
(7, '1.0.1', '2025-05-14', 'GODOT GAME\\Court Card Slash.zip', 'Added leaderboard\r\nFix the issue in card slot hovering\r\nAdded some card animations', '2025-05-20 02:51:27', '2025-05-20 02:51:27');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `role` enum('user','admin') DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `created_at`, `last_login`, `is_active`, `role`) VALUES
(1, 'andy', 'andy@gmail.com', '$2y$10$VXRk9NHn6G3T.qZ2fhqPhOXN16FKUkk5EisFPSavFe/wE376EK2KG', '2025-05-14 16:15:06', '2025-05-20 02:53:13', 1, 'user'),
(2, 'xakier', 'xakier@gmail.com', '$2y$10$JaBUBkcc95VHOEs65atJcOytfQdtb8PAMS6mx.e5cI8oZ.VTTxiR2', '2025-05-17 01:38:31', '2025-05-20 01:09:12', 1, 'user'),
(3, 'lsgsgelo', 'lesigesangelo@gmail.com', '$2y$10$NxzAF8BV8IDiIWLvR/6TOOQFO4.PX9Ls9LyjKAw5jXIgnLt2Qq4dS', '2025-05-17 06:09:20', NULL, 1, 'user'),
(10, 'x', 'x@gmail.com', '$2y$10$jz10YUekr3OrtHkDs/xhC.qNBId1tLFk61/uvF4lSTQ0f0mR./xum', '2025-05-19 01:48:50', '2025-05-19 01:48:54', 1, 'user');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `creators`
--
ALTER TABLE `creators`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `forum_comments`
--
ALTER TABLE `forum_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_forum_comments_post_id` (`post_id`);

--
-- Indexes for table `forum_likes`
--
ALTER TABLE `forum_likes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_like` (`post_id`,`user_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_forum_likes_post_id` (`post_id`);

--
-- Indexes for table `forum_posts`
--
ALTER TABLE `forum_posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_forum_posts_category` (`category`),
  ADD KEY `idx_forum_posts_user_id` (`user_id`);

--
-- Indexes for table `games`
--
ALTER TABLE `games`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_games_user_id` (`user_id`),
  ADD KEY `idx_games_score` (`score`);

--
-- Indexes for table `how_to_play`
--
ALTER TABLE `how_to_play`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `patch_notes`
--
ALTER TABLE `patch_notes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `creators`
--
ALTER TABLE `creators`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `forum_comments`
--
ALTER TABLE `forum_comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `forum_likes`
--
ALTER TABLE `forum_likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `forum_posts`
--
ALTER TABLE `forum_posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `games`
--
ALTER TABLE `games`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `how_to_play`
--
ALTER TABLE `how_to_play`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `patch_notes`
--
ALTER TABLE `patch_notes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `forum_comments`
--
ALTER TABLE `forum_comments`
  ADD CONSTRAINT `forum_comments_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `forum_posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `forum_comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `forum_likes`
--
ALTER TABLE `forum_likes`
  ADD CONSTRAINT `forum_likes_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `forum_posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `forum_likes_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `forum_posts`
--
ALTER TABLE `forum_posts`
  ADD CONSTRAINT `forum_posts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `games`
--
ALTER TABLE `games`
  ADD CONSTRAINT `games_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
