-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost
-- Généré le : ven. 24 oct. 2025 à 09:03
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `skuul_projet`
--

-- --------------------------------------------------------

--
-- Structure de la table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(191) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('laravel-cache-admin@gmail.com|127.0.0.1:timer', 'i:1761288293;', 1761288293),
('laravel-cache-admin@gmail.com|127.0.0.1', 'i:2;', 1761288293);

-- --------------------------------------------------------

--
-- Structure de la table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(191) NOT NULL,
  `owner` varchar(191) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `documents`
--

CREATE TABLE `documents` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `filename` varchar(191) NOT NULL,
  `path` varchar(191) NOT NULL,
  `mime` varchar(191) DEFAULT NULL,
  `size` bigint(20) DEFAULT NULL,
  `content` longtext DEFAULT NULL,
  `minhash` text DEFAULT NULL,
  `approved` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `locked` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `documents`
--

INSERT INTO `documents` (`id`, `user_id`, `filename`, `path`, `mime`, `size`, `content`, `minhash`, `approved`, `created_at`, `updated_at`, `locked`) VALUES
(7, 11, 'test.txt', 'private/documents/1761218624_P6mcqC_test.txt', 'text/plain', 66, 'Franck and his friend go to school only to satisfy their parents.jfjfjjfjfjfjf', '[411820264,14613737,433011634,250576259,458168463,314990242,531506952,13204899,182359348,497645659,535820798,1019447002,174480225,727082481,144591291,431336914,318220089,302271067,186677909,476700046,86889276,487307326,400396548,166832286,109155115,415168464,435128235,737202740,1003016444,54319322,172176693,186171643,485374903,217756557,321764130,300458907,405738811,332996044,927686905,1006239534,430561758,161689962,1046812722,25934814,26800735,740173192,979987878,589187911,941862349,383809199,227948350,60868027,752211017,478229559,114012497,13241306,1034906379,287481768,746448989,1013034029,999822769,74919407,459601145,208478825]', 0, '2025-10-23 09:23:44', '2025-10-24 04:44:37', 0),
(9, 11, 'plagiat.txt', 'private/documents/1761286624_fKivcB_plagiat.txt', 'text/plain', 66, 'Franck and his friend go to school only to satisfy their parents.dfghjkl', '[411820264,14613737,433011634,250576259,745861566,314990242,531506952,13204899,182359348,714248554,535820798,1019447002,174480225,727082481,144591291,21874794,318220089,302271067,186677909,476700046,86889276,707809551,400396548,166832286,109155115,415168464,435128235,737202740,1003016444,54319322,1031330820,186171643,485374903,217756557,321764130,300458907,405738811,332996044,927686905,1006239534,430561758,161689962,748577615,25934814,26800735,740173192,680662747,589187911,941862349,531469480,227948350,60868027,752211017,478229559,114012497,13241306,1034906379,287481768,746448989,1013034029,999822769,74919407,161451908,208478825]', 0, '2025-10-24 04:17:05', '2025-10-24 04:46:53', 0);

-- --------------------------------------------------------

--
-- Structure de la table `document_errors`
--

CREATE TABLE `document_errors` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `document_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `error_type` varchar(191) NOT NULL,
  `message` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `document_errors`
--

INSERT INTO `document_errors` (`id`, `document_id`, `user_id`, `error_type`, `message`, `created_at`, `updated_at`) VALUES
(24, 7, 11, 'too_short', 'Document trop court (13 mots, minimum 20 requis)', '2025-10-24 04:44:37', '2025-10-24 04:44:37'),
(25, 7, 11, 'similarity', 'Similaire au document ID 9 \'plagiat.txt\' (77.78%)', '2025-10-24 04:44:37', '2025-10-24 04:44:37'),
(28, 9, 11, 'too_short', 'Document trop court (13 mots, minimum 20 requis)', '2025-10-24 04:46:53', '2025-10-24 04:46:53'),
(29, 9, 11, 'similarity', 'Similaire au document ID 7 \'test.txt\' (77.78%)', '2025-10-24 04:46:53', '2025-10-24 04:46:53');

-- --------------------------------------------------------

--
-- Structure de la table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(191) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(191) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `jobs`
--

INSERT INTO `jobs` (`id`, `queue`, `payload`, `attempts`, `reserved_at`, `available_at`, `created_at`) VALUES
(3, 'default', '{\"uuid\":\"ed9af06f-8540-4b5b-b31d-0ddccae3a4b5\",\"displayName\":\"App\\\\Notifications\\\\ReportErrorResult\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:11;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:35:\\\"App\\\\Notifications\\\\ReportErrorResult\\\":2:{s:7:\\\"details\\\";s:5:\\\"HEllo\\\";s:2:\\\"id\\\";s:36:\\\"ebfe3c2a-b90e-4925-a030-4ad1ba3c0c88\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}}\"},\"createdAt\":1761286765,\"delay\":null}', 0, NULL, 1761286765, 1761286765);

-- --------------------------------------------------------

--
-- Structure de la table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(191) NOT NULL,
  `name` varchar(191) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(191) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_10_11_085439_create_students_table', 2),
(5, '2025_10_21_000001_create_documents_table', 3),
(6, '2025_10_21_000002_create_document_errors_table', 3),
(7, '2025_10_21_000003_create_reports_table', 3),
(8, '2025_10_21_000004_add_is_admin_to_users', 3),
(9, '2025_10_21_000005_add_minhash_to_documents', 4),
(10, '2025_10_21_000010_drop_is_admin_from_users', 5),
(11, '2025_10_23_000001_add_locked_to_documents_table', 6),
(12, '2025_10_23_141923_create_notifications_table', 7),
(13, '2025_10_23_000002_alter_status_column_in_reports_table', 8);

-- --------------------------------------------------------

--
-- Structure de la table `notifications`
--

CREATE TABLE `notifications` (
  `id` char(36) NOT NULL,
  `type` varchar(191) NOT NULL,
  `notifiable_type` varchar(191) NOT NULL,
  `notifiable_id` bigint(20) UNSIGNED NOT NULL,
  `data` text NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `notifications`
--

INSERT INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`) VALUES
('2266e96f-19c0-47cc-b9fe-0616d025b885', 'App\\Notifications\\ReportErrorResult', 'App\\Models\\User', 11, '{\"message\":\"R\\u00e9sultat de votre r\\u00e9clamation : jjjjjj\"}', '2025-10-24 04:10:58', '2025-10-24 03:59:33', '2025-10-24 04:10:58'),
('91efe9d3-fc7c-4ff4-954f-79ee31369642', 'App\\Notifications\\ReportErrorResult', 'App\\Models\\User', 11, '{\"message\":\"R\\u00e9sultat de votre r\\u00e9clamation : HEllo\"}', '2025-10-24 04:50:53', '2025-10-24 04:49:19', '2025-10-24 04:50:53'),
('a6a5aefe-fa8f-4f15-a602-8302e5bff8aa', 'App\\Notifications\\ReportErrorResult', 'App\\Models\\User', 11, '{\"message\":\"R\\u00e9sultat de votre r\\u00e9clamation : HEllo\"}', '2025-10-24 04:38:32', '2025-10-24 04:20:11', '2025-10-24 04:38:32');

-- --------------------------------------------------------

--
-- Structure de la table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(191) NOT NULL,
  `token` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `reports`
--

CREATE TABLE `reports` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `document_id` bigint(20) UNSIGNED DEFAULT NULL,
  `description` text NOT NULL,
  `status` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `reports`
--

INSERT INTO `reports` (`id`, `user_id`, `document_id`, `description`, `status`, `created_at`, `updated_at`) VALUES
(2, 11, 7, 'hhhhh', 'error_sent', '2025-10-23 12:47:39', '2025-10-23 12:50:18'),
(3, 11, 9, 'mwambesheye', 'error_sent', '2025-10-24 04:18:21', '2025-10-24 04:19:25');

-- --------------------------------------------------------

--
-- Structure de la table `role`
--

CREATE TABLE `role` (
  `id_role_user` int(11) NOT NULL,
  `role` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `role`
--

INSERT INTO `role` (`id_role_user`, `role`) VALUES
(1, 'admin'),
(3, 'user');

-- --------------------------------------------------------

--
-- Structure de la table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(191) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('zTDmIhrL0z3PMJYb5IuEfAQ91dlYAvhFfnu5lRQv', 11, '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'YTo2OntzOjY6Il90b2tlbiI7czo0MDoibkdCYzJlNWF4RG9pMm1heWxvZGRkQVJmYlR4bmhIMkpqNjdjSWl0aSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9kb2N1bWVudHMiO31zOjM6InVybCI7YTowOnt9czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTE7czo0OiJhdXRoIjthOjE6e3M6MjE6InBhc3N3b3JkX2NvbmZpcm1lZF9hdCI7aToxNzYxMjg4NjAzO319', 1761288653);

-- --------------------------------------------------------

--
-- Structure de la table `students`
--

CREATE TABLE `students` (
  `id_student` int(10) UNSIGNED NOT NULL,
  `student_name` varchar(191) NOT NULL,
  `student_email` varchar(191) NOT NULL,
  `student_birthday` int(11) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `students`
--

INSERT INTO `students` (`id_student`, `student_name`, `student_email`, `student_birthday`, `status`, `created_at`, `updated_at`) VALUES
(1, 'fleury', 'fleury@gmail.com', 2001, 0, '2025-10-11 08:18:51', '2025-10-11 08:18:51'),
(2, 'king', 'king@gmail', 2021, 0, '2025-10-11 08:19:29', '2025-10-11 08:19:29');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `email` varchar(191) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(191) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `id_role_user` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `id_role_user`) VALUES
(5, 'jordan', 'jordan@gmail.com', NULL, '$2y$12$azNYVxLHzXACQw1mF2mgou8OoxwS9uVONV7GMrs3Z7hruQ7nIs.9.', NULL, '2025-10-16 09:55:50', '2025-10-16 09:55:50', 1),
(6, 'nolan', 'nolan@gmail.com', NULL, '$2y$12$fRLhTliRpOP9keZwtbbrx.51ELWAP9.UzR/0LjHl0ehrHWyadPVye', NULL, '2025-10-16 09:58:52', '2025-10-16 09:58:52', 1),
(8, 'fleury', 'fleury@gmail.com', NULL, '$2y$12$9LmUxbvBes8WGvQWpssjeuZQSWxSR4vmbEKUC8pOfTgF03kA4ePJy', NULL, '2025-10-16 10:11:23', '2025-10-16 10:11:23', 3),
(9, 'john', 'john@gmail.com', NULL, '$2y$12$FsI7oCurf1kzgMU0fhiZjuv1.0Tn1iS5qKSV1QGZVjMTq.gO0asny', NULL, '2025-10-16 10:20:10', '2025-10-16 10:20:10', 3),
(10, 'franco', 'franco@gmail.com', NULL, '$2y$12$qX87pecQMMPBFLfMPCgJVeYNAdx1yOoIJdYnNmcEoCmw6Tya6OUOO', NULL, '2025-10-21 09:32:19', '2025-10-21 10:52:56', 1),
(11, 'sammy', 'sammy@gmail.com', NULL, '$2y$12$QBiGDML/On.cUWt5QW8V4OzDuEZN/f7EM1X7TSvRuPlZ3och16Hj6', 'fG5OecOW76VBQNHl73Ke3nPj9P37VVJsHmc83q95wZ0irM5n3Dmp1gfmoVkl', '2025-10-23 04:47:10', '2025-10-23 04:47:10', 3);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Index pour la table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Index pour la table `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `documents_user_id_foreign` (`user_id`);

--
-- Index pour la table `document_errors`
--
ALTER TABLE `document_errors`
  ADD PRIMARY KEY (`id`),
  ADD KEY `document_errors_document_id_foreign` (`document_id`),
  ADD KEY `document_errors_user_id_foreign` (`user_id`);

--
-- Index pour la table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Index pour la table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Index pour la table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`);

--
-- Index pour la table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Index pour la table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reports_user_id_foreign` (`user_id`),
  ADD KEY `reports_document_id_foreign` (`document_id`);

--
-- Index pour la table `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`id_role_user`);

--
-- Index pour la table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Index pour la table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id_student`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `fk_user_role` (`id_role_user`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `documents`
--
ALTER TABLE `documents`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT pour la table `document_errors`
--
ALTER TABLE `document_errors`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT pour la table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT pour la table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `role`
--
ALTER TABLE `role`
  MODIFY `id_role_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `students`
--
ALTER TABLE `students`
  MODIFY `id_student` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `documents`
--
ALTER TABLE `documents`
  ADD CONSTRAINT `documents_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `document_errors`
--
ALTER TABLE `document_errors`
  ADD CONSTRAINT `document_errors_document_id_foreign` FOREIGN KEY (`document_id`) REFERENCES `documents` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `document_errors_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `reports_document_id_foreign` FOREIGN KEY (`document_id`) REFERENCES `documents` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `reports_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_user_role` FOREIGN KEY (`id_role_user`) REFERENCES `role` (`id_role_user`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
