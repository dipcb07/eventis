SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+06:00";

CREATE TABLE `api_log` (
  `id` int(11) NOT NULL,
  `username` varchar(60) NOT NULL,
  `request_type` varchar(11) NOT NULL,
  `status` varchar(50) NOT NULL,
  `api_name` varchar(100) NOT NULL,
  `api_response` varchar(255) NOT NULL,
  `date_time` datetime(6) DEFAULT current_timestamp(6)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE `api_secret` (
  `id` int(11) NOT NULL,
  `active` tinyint(1) DEFAULT NULL,
  `secret_key` varchar(255) NOT NULL,
  `username` varchar(60) NOT NULL,
  `date_time` datetime(6) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

INSERT INTO `api_secret` (`id`, `active`, `secret_key`, `username`, `date_time`) VALUES
(2, 1, 'YmF0X3Rlc3RpbmdfYXBpXzI=', 'test_user', '2024-02-10 03:29:00.000000');

CREATE TABLE `attendees` (
  `id` int(11) NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `unique_id` varchar(255) NOT NULL,
  `event_id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `registration_date_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `unique_id` varchar(255) NOT NULL,
  `user_id` varchar(11) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `max_capacity` int(11) NOT NULL,
  `create_date_time` datetime NOT NULL,
  `update_date_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `unique_id` varchar(11) NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `name` varchar(100) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `org` varchar(255) DEFAULT NULL,
  `email_confirmation` tinyint(1) NOT NULL,
  `password` varchar(255) NOT NULL,
  `create_date_time` datetime NOT NULL DEFAULT current_timestamp(),
  `update_date_time` datetime(6) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `user_log` (
  `id` int(11) NOT NULL,
  `session_id` varchar(100) NOT NULL,
  `user_id` varchar(100) NOT NULL,
  `logged_ip` varchar(50) NOT NULL,
  `start_date_time` datetime NOT NULL,
  `end_date_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `api_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `username` (`username`,`api_name`);

ALTER TABLE `api_secret`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD KEY `secret_key` (`secret_key`,`username`);

ALTER TABLE `attendees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_id` (`unique_id`),
  ADD KEY `email_2` (`email`);

ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_id` (`unique_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `is_active` (`is_active`),
  ADD KEY `start_date` (`start_date`,`end_date`),
  ADD KEY `name` (`name`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `unique_id_2` (`unique_id`,`username`,`email`),
  ADD KEY `unique_id` (`unique_id`,`username`,`email`);

ALTER TABLE `user_log`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `api_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `attendees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `user_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;