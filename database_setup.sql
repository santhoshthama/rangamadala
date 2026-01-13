-- Rangamadala Database Setup
-- Run this SQL script in your rangamandala_db database

-- Create users table
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `role` enum('admin','artist','audience','service_provider') NOT NULL DEFAULT 'audience',
  `nic_photo` varchar(255) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `years_experience` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create categories table
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create dramas table
CREATE TABLE IF NOT EXISTS `dramas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `drama_name` varchar(255) NOT NULL COMMENT 'Drama name as in public performance board certificate',
  `certificate_number` varchar(100) NOT NULL COMMENT 'Public performance certificate number',
  `owner_name` varchar(255) NOT NULL COMMENT 'Owner name',
  `certificate_image` varchar(255) DEFAULT NULL COMMENT 'Image of public performance board certificate',
  `created_by` int(11) DEFAULT NULL,
  `creator_artist_id` int(11) DEFAULT NULL COMMENT 'The artist who is the director',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `certificate_number` (`certificate_number`),
  KEY `created_by` (`created_by`),
  KEY `creator_artist_id` (`creator_artist_id`),
  CONSTRAINT `dramas_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `dramas_ibfk_3` FOREIGN KEY (`creator_artist_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert sample categories
INSERT INTO `categories` (`name`) VALUES
('Classical Drama'),
('Musical Drama'),
('Comedy Drama'),
('Traditional Dance'),
('Modern Theatre'),
('Street Drama'),
('Folk Theatre'),
('Experimental Theatre')
ON DUPLICATE KEY UPDATE name=name;

-- Insert sample dramas (optional - remove if not needed)
INSERT INTO `dramas` (`drama_name`, `certificate_number`, `owner_name`, `certificate_image`, `created_by`, `creator_artist_id`) VALUES
('Maname', 'PPB-2025-001', 'Chandrasena Perera', NULL, NULL, NULL),
('Sinhabahu', 'PPB-2025-002', 'Ediriweera Sarachchandra', NULL, NULL, NULL)
ON DUPLICATE KEY UPDATE drama_name=VALUES(drama_name);

-- Create serviceprovider table
CREATE TABLE IF NOT EXISTS `serviceprovider` (
  `user_id` int NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `professional_title` varchar(100) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `years_experience` int DEFAULT NULL,
  `professional_summary` text,
  `availability` tinyint(1) DEFAULT '1',
  `availability_notes` varchar(255) DEFAULT NULL,
  `business_cert_photo` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`),
  CONSTRAINT `serviceprovider_ibfk_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create services table
CREATE TABLE IF NOT EXISTS `services` (
  `id` int NOT NULL AUTO_INCREMENT,
  `provider_id` int NOT NULL,
  `service_name` varchar(100) NOT NULL,
  `rate_per_hour` decimal(10,2) DEFAULT NULL,
  `description` text,
  PRIMARY KEY (`id`),
  KEY `provider_id` (`provider_id`),
  CONSTRAINT `services_ibfk_1` FOREIGN KEY (`provider_id`) REFERENCES `serviceprovider` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create projects table
CREATE TABLE IF NOT EXISTS `projects` (
  `id` int NOT NULL AUTO_INCREMENT,
  `provider_id` int NOT NULL,
  `year` int DEFAULT NULL,
  `project_name` varchar(100) DEFAULT NULL,
  `services_provided` varchar(255) DEFAULT NULL,
  `description` text,
  PRIMARY KEY (`id`),
  KEY `provider_id` (`provider_id`),
  CONSTRAINT `projects_ibfk_1` FOREIGN KEY (`provider_id`) REFERENCES `serviceprovider` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
